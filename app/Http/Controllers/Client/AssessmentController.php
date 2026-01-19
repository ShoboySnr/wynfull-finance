<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentProgress;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use App\Models\ResourceCollection;
use App\Models\ResourceModule;
use App\Services\ModuleCompletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function show(ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        // Ensure module is an assessment type
        abort_unless($resourceModule->type === 'assessment', 404);
        abort_unless($resourceModule->resource_collection_id == $resourceCollection->id, 404);

        // Load questions with options
        $questions = $resourceModule->assessmentQuestions()->with('options')->get();

        // Get previous submissions for this user
        $previousSubmissions = AssessmentSubmission::where('resource_module_id', $resourceModule->id)
            ->where('user_id', auth()->id())
            ->orderBy('submitted_at', 'desc')
            ->get();

        return view('client.assessments.show', [
            'resourceCollection' => $resourceCollection,
            'module' => $resourceModule,
            'questions' => $questions,
            'previousSubmissions' => $previousSubmissions,
        ]);
    }

    public function submit(Request $request, ResourceCollection $resourceCollection, ResourceModule $resourceModule)
    {
        abort_unless($resourceModule->type === 'assessment', 404);
        abort_unless($resourceModule->resource_collection_id == $resourceCollection->id, 404);

        $data = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['nullable'],
        ]);

        DB::beginTransaction();
        try {
            // Create submission
            $submission = AssessmentSubmission::create([
                'resource_module_id' => $resourceModule->id,
                'user_id' => auth()->id(),
                'submitted_at' => now(),
            ]);

            $answersCreated = 0;

            // Process each answer
            foreach ($data['answers'] as $questionId => $answer) {
                // Skip unanswered questions
                if (empty($answer) && $answer !== '0') {
                    continue;
                }

                $question = AssessmentQuestion::with('options')->find($questionId);
                
                if (!$question || $question->resource_module_id != $resourceModule->id) {
                    continue;
                }

                $selectedOptionId = null;
                $answerText = null;

                // Store answer based on question type
                switch ($question->question_type) {
                    case 'multiple_choice':
                    case 'true_false':
                        $selectedOptionId = $answer;
                        break;

                    case 'short_answer':
                    case 'essay':
                        $answerText = $answer;
                        break;
                }

                // Create answer record
                $createdAnswer = AssessmentAnswer::create([
                    'assessment_submission_id' => $submission->id,
                    'assessment_question_id' => $question->id,
                    'assessment_question_option_id' => $selectedOptionId,
                    'answer_text' => $answerText,
                ]);

                $answersCreated++;
            }

            // Mark module as complete (first submission counts as completion)
            $completionService = app(ModuleCompletionService::class);
            $completionService->complete(auth()->user(), $resourceModule);

            DB::commit();

            activity()->useLog('content')
                ->causedBy(auth()->user())
                ->performedOn($submission)
                ->event('assessment_submitted')
                ->log('Submitted assessment');

            return redirect()->route('client.assessments.results', [$resourceCollection, $resourceModule, $submission])
                ->with('success', 'Assessment submitted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to submit assessment. Please try again.')
                ->withInput();
        }
    }

    public function results(ResourceCollection $resourceCollection, ResourceModule $resourceModule, AssessmentSubmission $submission)
    {
        abort_unless($resourceModule->type === 'assessment', 404);
        abort_unless($resourceModule->resource_collection_id == $resourceCollection->id, 404);
        abort_unless($submission->resource_module_id == $resourceModule->id, 404);
        abort_unless($submission->user_id == auth()->id(), 403);

        $submission->load(['answers.question.options', 'answers.selectedOption']);

        return view('client.assessments.results', [
            'resourceCollection' => $resourceCollection,
            'module' => $resourceModule,
            'submission' => $submission,
        ]);
    }
}

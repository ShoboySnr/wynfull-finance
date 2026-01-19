<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentSubmission;
use App\Models\ResourceModule;
use Illuminate\Http\Request;

class AssessmentQuestionController extends Controller
{
    public function index(ResourceModule $module)
    {
        // Ensure module is an assessment type
        abort_unless($module->type === 'assessment', 404);

        $questions = $module->assessmentQuestions()->with('options')->get();

        return view('admin.resources.assessments.index', [
            'module' => $module,
            'questions' => $questions
        ]);
    }

    public function store(Request $request, ResourceModule $module)
    {
        abort_unless($module->type === 'assessment', 404);

        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:multiple_choice,true_false,short_answer,essay'],
            'options' => ['required_if:question_type,multiple_choice,true_false', 'array'],
            'options.*.option_text' => ['required_with:options', 'string'],
        ]);

        // Get next sort order
        $nextSortOrder = $module->assessmentQuestions()->max('sort_order') + 1;

        $question = AssessmentQuestion::create([
            'resource_module_id' => $module->id,
            'question_text' => $data['question_text'],
            'question_type' => $data['question_type'],
            'sort_order' => $nextSortOrder,
        ]);

        // Create options if provided
        if (!empty($data['options'])) {
            foreach ($data['options'] as $index => $option) {
                AssessmentQuestionOption::create([
                    'assessment_question_id' => $question->id,
                    'option_text' => $option['option_text'],
                    'sort_order' => $index,
                ]);
            }
        }

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($question)
            ->event('assessment_question_created')
            ->log('Created assessment question');

        return redirect()->back()->with('success', 'Question added successfully');
    }

    public function update(Request $request, ResourceModule $module, AssessmentQuestion $question)
    {
        abort_unless($module->type === 'assessment', 404);
        abort_unless($question->resource_module_id === $module->id, 404);

        $data = $request->validate([
            'question_text' => ['required', 'string'],
            'question_type' => ['required', 'in:multiple_choice,true_false,short_answer,essay'],
            'options' => ['required_if:question_type,multiple_choice,true_false', 'array'],
            'options.*.id' => ['nullable', 'exists:assessment_question_options,id'],
            'options.*.option_text' => ['required_with:options', 'string'],
        ]);

        $question->update([
            'question_text' => $data['question_text'],
            'question_type' => $data['question_type'],
        ]);

        // Update or create options
        if (!empty($data['options'])) {
            $existingOptionIds = [];

            foreach ($data['options'] as $index => $optionData) {
                if (!empty($optionData['id'])) {
                    // Update existing option
                    $option = AssessmentQuestionOption::find($optionData['id']);
                    if ($option && $option->assessment_question_id === $question->id) {
                        $option->update([
                            'option_text' => $optionData['option_text'],
                            'sort_order' => $index,
                        ]);
                        $existingOptionIds[] = $option->id;
                    }
                } else {
                    // Create new option
                    $option = AssessmentQuestionOption::create([
                        'assessment_question_id' => $question->id,
                        'option_text' => $optionData['option_text'],
                        'sort_order' => $index,
                    ]);
                    $existingOptionIds[] = $option->id;
                }
            }

            // Delete options that were removed
            $question->options()->whereNotIn('id', $existingOptionIds)->delete();
        }

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($question)
            ->event('assessment_question_updated')
            ->log('Updated assessment question');

        return redirect()->back()->with('success', 'Question updated successfully');
    }

    public function destroy(Request $request, ResourceModule $module, AssessmentQuestion $question)
    {
        abort_unless($module->type === 'assessment', 404);
        abort_unless($question->resource_module_id === $module->id, 404);

        $question->delete();

        activity()->useLog('content')
            ->causedBy($request->user())
            ->performedOn($question)
            ->event('assessment_question_deleted')
            ->log('Deleted assessment question');

        return redirect()->back()->with('success', 'Question deleted successfully');
    }

    public function reorder(Request $request, ResourceModule $module)
    {
        abort_unless($module->type === 'assessment', 404);

        $data = $request->validate([
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['required', 'exists:assessment_questions,id'],
        ]);

        foreach ($data['ordered_ids'] as $index => $questionId) {
            AssessmentQuestion::where('id', $questionId)
                ->where('resource_module_id', $module->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function results(ResourceModule $module)
    {
        abort_unless($module->type === 'assessment', 404);

        $submissions = $module->assessmentSubmissions()
            ->with(['user.clientProfile'])
            ->orderByDesc('submitted_at')
            ->get();

        $stats = [
            'total_submissions' => $submissions->count(),
            'average_score' => $submissions->avg('percentage'),
            'highest_score' => $submissions->max('percentage'),
            'lowest_score' => $submissions->min('percentage'),
            'completion_rate' => $submissions->count() > 0 ? 100 : 0,
        ];

        return view('admin.resources.assessments.results', [
            'module' => $module,
            'submissions' => $submissions,
            'stats' => $stats,
        ]);
    }

    public function viewSubmission(ResourceModule $module, AssessmentSubmission $submission)
    {
        abort_unless($module->type === 'assessment', 404);
        abort_unless($submission->resource_module_id === $module->id, 404);

        $submission->load([
            'user.clientProfile',
            'answers.question.options',
            'answers.selectedOption'
        ]);

        return view('admin.resources.assessments.view-submission', [
            'module' => $module,
            'submission' => $submission,
        ]);
    }
}

<!-- Onboarding Modal -->
<div class="modal-overlay" id="onboardingModal">
    <div class="modal-content">
        <div class="modal-header">
            <h1>Welcome to Wynfull Finance</h1>
            <p>Let's get started on your financial education and solution journey</p>
        </div>
        <div class="onboarding-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="step-counter">
                <span id="currentStep">1</span> of <span id="totalSteps">7</span>
            </div>

            <!-- Error Message Container -->
            <div class="error-message" id="errorMessage" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="errorText"></span>
            </div>

            <div class="onboarding-step active" id="step1">
                <h2>Which statement best reflects your current financial learning focus?</h2>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="financial-situation" value="debt-management"> Building a stronger understanding of debt management</label>
                    <label><input type="checkbox" name="financial-situation" value="cash-flow"> Learning how to manage cash flow more effectively</label>
                    <label><input type="checkbox" name="financial-situation" value="savings-habits"> Improving savings habits and consistency</label>
                    <label><input type="checkbox" name="financial-situation" value="investing-basics"> Learning how investing works and how to get started</label>
                    <label><input type="checkbox" name="financial-situation" value="wealth-building"> Strengthening long-term wealth-building skills</label>
                    <label><input type="checkbox" name="financial-situation" value="financial-education"> Exploring financial education broadly</label>
                    <label class="other-option">
                        <div>
                            <input type="checkbox" name="financial-situation" value="other"> Other:
                        </div>
                        <input type="text" id="financial-situation-other" placeholder="Please specify..." class="other-input">
                    </label>
                </div>
            </div>

            <div class="onboarding-step" id="step2">
                <h2>How confident are you in understanding how a personal budget works?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="primary-goal" value="not-confident"> Not confident</label>
                    <label><input type="radio" name="primary-goal" value="somewhat-confident"> Somewhat confident</label>
                    <label><input type="radio" name="primary-goal" value="confident"> Confident</label>
                    <label><input type="radio" name="primary-goal" value="very-confident"> Very confident</label>
                </div>
            </div>

            <div class="onboarding-step" id="step3">
                <h2>How confident do you feel understanding core personal financial concepts?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="confidence-level" value="not-confident"> Not confident</label>
                    <label><input type="radio" name="confidence-level" value="somewhat-confident"> Somewhat confident</label>
                    <label><input type="radio" name="confidence-level" value="confident"> Confident</label>
                    <label><input type="radio" name="confidence-level" value="very-confident"> Very confident</label>
                </div>
            </div>

            <div class="onboarding-step" id="step4">
                <h2>How would you describe your understanding of debt management strategies?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="debt-feeling" value="no-knowledge"> I have no knowledge of debt management strategies</label>
                    <label><input type="radio" name="debt-feeling" value="basics-stressful"> I understand the basics but find them stressful to apply</label>
                    <label><input type="radio" name="debt-feeling" value="comfortable-applying"> I'm comfortable applying debt management strategies</label>
                    <label><input type="radio" name="debt-feeling" value="confident-teaching"> I'm confident teaching or explaining debt strategies</label>
                </div>
            </div>

            <div class="onboarding-step" id="step5">
                <h2>How confident are you in understanding the steps involved in preparing for unexpected financial situations?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="savings-amount" value="not-confident"> Not confident</label>
                    <label><input type="radio" name="savings-amount" value="somewhat-confident"> Somewhat confident</label>
                    <label><input type="radio" name="savings-amount" value="confident"> Confident</label>
                    <label><input type="radio" name="savings-amount" value="very-confident"> Very confident</label>
                </div>
            </div>

            <div class="onboarding-step" id="step6">
                <h2>How familiar are you with investing concepts?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="investing-status" value="not-familiar"> Not familiar yet</label>
                    <label><input type="radio" name="investing-status" value="familiar-basics"> Familiar with basics</label>
                    <label><input type="radio" name="investing-status" value="comfortable-applying"> Comfortable applying concepts</label>
                    <label><input type="radio" name="investing-status" value="advanced-understanding"> Advanced understanding</label>
                </div>
            </div>

            <div class="onboarding-step" id="step7">
                <h2>What's your investing experience?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="investing-experience" value="beginner"> Beginner</label>
                    <label><input type="radio" name="investing-experience" value="intermediate"> Intermediate</label>
                    <label><input type="radio" name="investing-experience" value="advanced"> Advanced</label>
                </div>
            </div>

            <div class="onboarding-actions">
                <button class="btn-secondary" id="prevBtn" style="display: none;">Previous</button>
                <button class="btn-primary" id="nextBtn">Next</button>
            </div>
        </div>
    </div>
</div>

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
                <h2>Which best describes your current financial situation?</h2>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="financial-situation" value="struggling-debt"> Struggling with debt</label>
                    <label><input type="checkbox" name="financial-situation" value="paycheck-to-paycheck"> Living paycheck-to-paycheck</label>
                    <label><input type="checkbox" name="financial-situation" value="okay-not-saving"> Doing okay but not saving much</label>
                    <label><input type="checkbox" name="financial-situation" value="saving-regularly"> Saving regularly and want to invest</label>
                    <label><input type="checkbox" name="financial-situation" value="confident-focused"> Confident and focused on long-term wealth</label>
                    <label class="other-option">
                        <div>
                            <input type="checkbox" name="financial-situation" value="other"> Other:
                        </div>
                        <input type="text" id="financial-situation-other" placeholder="Please specify..." class="other-input">
                    </label>
                </div>
            </div>

            <div class="onboarding-step" id="step2">
                <h2>What's your #1 money goal for the next 12 months?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="primary-goal" value="pay-off-debt"> Pay off or reduce debt</label>
                    <label><input type="radio" name="primary-goal" value="emergency-fund"> Build an emergency fund</label>
                    <label><input type="radio" name="primary-goal" value="big-purchase"> Save for a big purchase (home, car, travel)</label>
                    <label><input type="radio" name="primary-goal" value="start-investing"> Start investing or invest more</label>
                    <label><input type="radio" name="primary-goal" value="wealth-retirement"> Grow wealth for retirement/financial independence</label>
                    <label class="other-option">
                        <div>
                            <input type="radio" name="primary-goal" value="other"> Other:
                        </div>
                        <input type="text" id="primary-goal-other" placeholder="Please specify..." class="other-input">
                    </label>
                </div>
            </div>

            <div class="onboarding-step" id="step3">
                <h2>How confident do you feel managing your finances right now?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="confidence-level" value="not-confident"> Not confident</label>
                    <label><input type="radio" name="confidence-level" value="somewhat-confident"> Somewhat confident</label>
                    <label><input type="radio" name="confidence-level" value="confident"> Confident</label>
                    <label><input type="radio" name="confidence-level" value="very-confident"> Very confident</label>
                </div>
            </div>

            <div class="onboarding-step" id="step4">
                <h2>How do you feel about your current debt?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="debt-feeling" value="overwhelmed"> I feel overwhelmed</label>
                    <label><input type="radio" name="debt-feeling" value="managing-stressful"> I'm managing but it's stressful</label>
                    <label><input type="radio" name="debt-feeling" value="comfortable"> I'm comfortable with it</label>
                    <label><input type="radio" name="debt-feeling" value="debt-free"> I'm debt-free</label>
                </div>
            </div>

            <div class="onboarding-step" id="step5">
                <h2>How much cash do you currently have saved (emergency or otherwise)?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="savings-amount" value="zero"> $0</label>
                    <label><input type="radio" name="savings-amount" value="under-1k"> Less than $1,000</label>
                    <label><input type="radio" name="savings-amount" value="1k-5k"> $1,000–$5,000</label>
                    <label><input type="radio" name="savings-amount" value="5k-20k"> $5,000–$20,000</label>
                    <label><input type="radio" name="savings-amount" value="20k-plus"> $20,000+</label>
                </div>
            </div>

            <div class="onboarding-step" id="step6">
                <h2>Do you currently invest money, e.g. stock market (outside of savings)?</h2>
                <div class="radio-group">
                    <label><input type="radio" name="investing-status" value="not-yet"> Not yet</label>
                    <label><input type="radio" name="investing-status" value="just-starting"> Yes, but just starting</label>
                    <label><input type="radio" name="investing-status" value="consistently"> Yes, consistently</label>
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

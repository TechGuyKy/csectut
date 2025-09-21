<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit;
}

$user = currentUser();
$user_id = $_SESSION['user_id'] ?? 0;
$username = $user['username'] ?? 'User';

$tutorial_id = 'ddos-defense';
$tutorial_completed = false;

if (function_exists('isTutorialCompleted')) {
    $tutorial_completed = isTutorialCompleted($tutorial_id, $user_id);
}

$current_time = new DateTime('2025-09-20 07:00:32', new DateTimeZone('UTC'));
$timestamp = $current_time->format('Y-m-d H:i:s');

$quiz_questions = [
    [
        'id' => 'q1',
        'question' => 'What is the amplification factor of a DNS reflection attack using DNSSEC responses?',
        'options' => [
            'Up to 28:1 amplification ratio',
            'Up to 54:1 amplification ratio',
            'Up to 179:1 amplification ratio',
            'Up to 206:1 amplification ratio'
        ],
        'correct' => 1,
        'explanation' => 'DNS amplification attacks using DNSSEC can achieve amplification ratios of up to 54:1, making them extremely effective for volumetric attacks.'
    ],
    [
        'id' => 'q2',
        'question' => 'Which BGP community attribute is used for upstream provider DDoS mitigation?',
        'options' => [
            'NO_EXPORT (65535:65281)',
            'BLACKHOLE (65535:666)',
            'LOCAL_PREF (65535:100)',
            'MED (65535:200)'
        ],
        'correct' => 1,
        'explanation' => 'The BLACKHOLE community (65535:666) is a well-known BGP community used to signal upstream providers to drop traffic to specific prefixes during DDoS attacks.'
    ],
    [
        'id' => 'q3',
        'question' => 'In a SYN flood attack, what is the typical TCP window size used to maximize resource exhaustion?',
        'options' => [
            'Window size 0 (zero window)',
            'Window size 65535 (maximum)',
            'Window size 1024 (small)',
            'Window size varies randomly'
        ],
        'correct' => 0,
        'explanation' => 'Attackers often use TCP window size 0 to keep connections open indefinitely, preventing the server from closing connections and maximizing resource exhaustion.'
    ],
    [
        'id' => 'q4',
        'question' => 'What is the primary advantage of using FlowSpec (RFC 5575) for DDoS mitigation?',
        'options' => [
            'Provides encryption for mitigation rules',
            'Enables automated distribution of filtering rules via BGP',
            'Increases network bandwidth automatically',
            'Blocks all UDP traffic by default'
        ],
        'correct' => 1,
        'explanation' => 'FlowSpec allows network operators to distribute fine-grained filtering rules automatically via BGP, enabling rapid and coordinated DDoS response across network infrastructure.'
    ],
    [
        'id' => 'q5',
        'question' => 'Which technique is most effective for detecting low-and-slow application layer attacks?',
        'options' => [
            'Bandwidth threshold monitoring',
            'Connection rate analysis',
            'Behavioral pattern analysis and entropy measurement',
            'IP geolocation filtering'
        ],
        'correct' => 2,
        'explanation' => 'Low-and-slow attacks require behavioral analysis and entropy measurement to detect subtle patterns that don\'t trigger traditional volume-based detection systems.'
    ],
    [
        'id' => 'q6',
        'question' => 'What is the optimal scrubbing center deployment model for global DDoS protection?',
        'options' => [
            'Single centralized scrubbing center',
            'Regional scrubbing centers with anycast routing',
            'On-premises scrubbing only',
            'Cloud-only scrubbing without physical infrastructure'
        ],
        'correct' => 1,
        'explanation' => 'Regional scrubbing centers with anycast routing provide the best balance of global coverage, low latency, and distributed capacity for handling large-scale DDoS attacks.'
    ],
    [
        'id' => 'q7',
        'question' => 'In advanced DDoS defense, what is the purpose of implementing rate limiting with token bucket algorithms?',
        'options' => [
            'To completely block all traffic above threshold',
            'To provide burst tolerance while maintaining average rate limits',
            'To encrypt traffic at rate-limited connections',
            'To redirect traffic to alternative servers'
        ],
        'correct' => 1,
        'explanation' => 'Token bucket algorithms allow for burst traffic tolerance while maintaining long-term average rate limits, providing more flexible and effective DDoS mitigation than simple rate limiting.'
    ],
    [
        'id' => 'q8',
        'question' => 'What is the primary challenge in defending against carpet bombing attacks?',
        'options' => [
            'High bandwidth consumption',
            'Distributed targeting across multiple IP ranges',
            'Use of encrypted attack vectors',
            'Advanced evasion techniques'
        ],
        'correct' => 1,
        'explanation' => 'Carpet bombing attacks target multiple IP addresses across an organization\'s IP space simultaneously, making traditional single-target mitigation strategies less effective.'
    ]
];

if (!isset($_SESSION['quiz_order_' . $tutorial_id])) {
    $_SESSION['quiz_order_' . $tutorial_id] = range(0, count($quiz_questions) - 1);
    shuffle($_SESSION['quiz_order_' . $tutorial_id]);
}

$randomized_questions = [];
foreach ($_SESSION['quiz_order_' . $tutorial_id] as $index) {
    $question = $quiz_questions[$index];
    if (!isset($_SESSION['option_order_' . $question['id']])) {
        $options = $question['options'];
        $correct_answer = $options[$question['correct']];
        
        $option_indices = range(0, count($options) - 1);
        shuffle($option_indices);
        
        $new_options = [];
        $new_correct = 0;
        foreach ($option_indices as $new_index => $old_index) {
            $new_options[] = $options[$old_index];
            if ($old_index === $question['correct']) {
                $new_correct = $new_index;
            }
        }
        
        $question['options'] = $new_options;
        $question['correct'] = $new_correct;
        $_SESSION['option_order_' . $question['id']] = $question;
    } else {
        $question = $_SESSION['option_order_' . $question['id']];
    }
    
    $randomized_questions[] = $question;
}
?>

<style>
    :root {
        --ddos-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --attack-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
        --defense-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --step-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --dark-bg: #0f0f23;
        --card-bg: #1a1a2e;
        --text-primary: #ffffff;
        --text-secondary: #a8b2d1;
        --border-color: rgba(255, 255, 255, 0.1);
        --success-green: #00b894;
        --warning-orange: #e17055;
        --danger-red: #ff6b6b;
    }

    body {
        background: var(--dark-bg);
        color: var(--text-primary);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        line-height: 1.6;
    }

    .tutorial-hero {
        background: var(--ddos-gradient);
        color: white;
        padding: 4rem 0;
        margin: 0 -15px 3rem -15px;
        position: relative;
        overflow: hidden;
    }

    .tutorial-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="ddos-pattern" width="25" height="25" patternUnits="userSpaceOnUse"><circle cx="12.5" cy="12.5" r="1.5" fill="rgba(255,255,255,0.1)"/><path d="M 25 0 L 0 0 0 25" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23ddos-pattern)"/></svg>');
        opacity: 0.4;
    }

    .tutorial-hero .container {
        position: relative;
        z-index: 2;
    }

    .step-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 3rem;
        position: relative;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .step-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--step-gradient);
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }

    .step-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .step-number {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: var(--step-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 10px 30px rgba(250, 112, 154, 0.3);
    }

    .step-info h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        color: var(--text-primary);
    }

    .step-info p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 1rem;
        line-height: 1.5;
    }

    .step-content {
        display: grid;
        gap: 2rem;
    }

    .concept-box {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        border-left: 4px solid var(--success-green);
    }

    .concept-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--success-green);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .concept-content {
        color: var(--text-secondary);
        line-height: 1.6;
    }

    .technical-details {
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid rgba(79, 172, 254, 0.3);
        border-radius: 12px;
        padding: 2rem;
        margin: 1.5rem 0;
        border-left: 4px solid #4facfe;
    }

    .technical-title {
        color: #4facfe;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .code-block {
        background: #000;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 1.5rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        margin: 1rem 0;
        position: relative;
        overflow-x: auto;
        color: #e6edf3;
    }

    .code-header {
        background: #1a1a1a;
        color: #888;
        padding: 0.5rem 1rem;
        border-radius: 8px 8px 0 0;
        margin: -1.5rem -1.5rem 1rem -1.5rem;
        font-size: 0.8rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .command-line {
        color: #00ff41;
    }

    .config-line {
        color: #74b9ff;
    }

    .comment-line {
        color: #6a9955;
    }

    .attack-demo {
        background: rgba(255, 107, 107, 0.1);
        border: 1px solid rgba(255, 107, 107, 0.3);
        border-radius: 16px;
        padding: 2rem;
        margin: 2rem 0;
        border-left: 4px solid var(--danger-red);
    }

    .demo-title {
        color: var(--danger-red);
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .defense-strategy {
        background: rgba(67, 233, 123, 0.1);
        border: 1px solid rgba(67, 233, 123, 0.3);
        border-radius: 16px;
        padding: 2rem;
        margin: 2rem 0;
        border-left: 4px solid var(--success-green);
    }

    .strategy-title {
        color: var(--success-green);
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .implementation-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin: 2rem 0;
    }

    .implementation-card {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .implementation-card:hover {
        transform: translateY(-3px);
        border-color: var(--success-green);
        box-shadow: 0 10px 25px rgba(0, 184, 148, 0.2);
    }

    .card-header {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .technique-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .technique-list li {
        padding: 0.8rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .technique-list li:last-child {
        border-bottom: none;
    }

    .technique-list li::before {
        content: '▶';
        color: var(--success-green);
        font-size: 0.8rem;
        margin-top: 0.1rem;
        flex-shrink: 0;
    }

    .progression-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin: 3rem 0;
        padding: 2rem;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .progress-step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
    }

    .progress-step.completed {
        background: var(--success-green);
        color: white;
    }

    .progress-step.active {
        background: var(--step-gradient);
        color: white;
        animation: pulse 2s infinite;
    }

    .progress-line {
        width: 60px;
        height: 2px;
        background: rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .progress-line.completed {
        background: var(--success-green);
    }

    .quiz-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 3rem;
        margin: 3rem 0;
        position: relative;
        overflow: hidden;
    }

    .quiz-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--ddos-gradient);
    }

    .quiz-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        color: var(--text-primary);
    }

    .quiz-description {
        text-align: center;
        color: var(--text-secondary);
        margin-bottom: 3rem;
        font-size: 1.1rem;
    }

    .quiz-question {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .question-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .question-number {
        background: var(--success-green);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        flex-shrink: 0;
    }

    .question-text {
        font-size: 1.1rem;
        font-weight: 500;
        color: var(--text-primary);
        line-height: 1.5;
    }

    .question-options {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .option {
        background: rgba(0, 0, 0, 0.3);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
    }

    .option:hover {
        border-color: var(--success-green);
        background: rgba(0, 184, 148, 0.1);
    }

    .option.selected {
        border-color: #4facfe;
        background: rgba(79, 172, 254, 0.1);
    }

    .option.correct {
        border-color: var(--success-green);
        background: rgba(0, 184, 148, 0.1);
    }

    .option.incorrect {
        border-color: var(--danger-red);
        background: rgba(255, 107, 107, 0.1);
    }

    .option-letter {
        background: var(--success-green);
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .option.correct .option-letter {
        background: var(--success-green);
    }

    .option.incorrect .option-letter {
        background: var(--danger-red);
    }

    .option-text {
        color: var(--text-primary);
        font-weight: 500;
    }

    .explanation {
        background: rgba(79, 172, 254, 0.1);
        border: 1px solid rgba(79, 172, 254, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1rem;
        display: none;
    }

    .explanation.show {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    .explanation-title {
        font-weight: 600;
        color: #4facfe;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .explanation-text {
        color: var(--text-secondary);
        line-height: 1.5;
    }

    .quiz-controls {
        text-align: center;
        margin-top: 3rem;
    }

    .quiz-btn {
        background: var(--ddos-gradient);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .quiz-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
    }

    .quiz-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .quiz-results {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        margin-top: 2rem;
        display: none;
    }

    .quiz-results.show {
        display: block;
        animation: slideUp 0.5s ease;
    }

    .results-score {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        background: var(--ddos-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .results-message {
        font-size: 1.2rem;
        margin-bottom: 2rem;
        color: var(--text-secondary);
    }

    .completion-status {
        background: linear-gradient(135deg, rgba(0, 184, 148, 0.1), rgba(0, 206, 201, 0.05));
        border: 1px solid rgba(0, 184, 148, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem 0;
        text-align: center;
    }

    .completion-icon {
        font-size: 3rem;
        color: var(--success-green);
        margin-bottom: 1rem;
    }

    .completion-text {
        font-size: 1.1rem;
        color: var(--success-green);
        font-weight: 600;
    }

    .navigation {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        margin: 3rem 0;
    }

    .nav-btn {
        background: var(--ddos-gradient);
        color: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .nav-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 233, 123, 0.4);
        color: white;
        text-decoration: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @media (max-width: 768px) {
        .tutorial-hero {
            padding: 2rem 0;
            margin: 0 -15px 2rem -15px;
        }

        .step-header {
            flex-direction: column;
            text-align: center;
        }

        .step-number {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }

        .implementation-grid {
            grid-template-columns: 1fr;
        }

        .progression-indicator {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .quiz-section {
            padding: 2rem 1rem;
        }

        .question-options {
            gap: 0.8rem;
        }
    }
</style>

<div class="tutorial-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">
                    🛡️ Advanced DDoS Defense Mastery
                </h1>
                <p class="lead mb-4">
                    Master enterprise-grade DDoS defense through comprehensive analysis of attack vectors, detection methodologies, and multi-layered mitigation strategies. Learn real-world implementation techniques used by security professionals.
                </p>
                
                <div style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 1rem; margin-top: 1rem;">
                    <div style="color: #00ff41; font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-shield-alt"></i> Advanced Defense Training
                    </div>
                    <div style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">
                        This tutorial covers advanced concepts including BGP blackholing, FlowSpec implementation, and enterprise-grade scrubbing center deployment.
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 15px; padding: 1.5rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <h5 style="margin-bottom: 1rem; color: white;">Learning Objectives</h5>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; text-align: center;">
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #43e97b;">8</div>
                            <div style="font-size: 0.8rem; opacity: 0.8;">Defense Steps</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #4facfe;">150</div>
                            <div style="font-size: 0.8rem; opacity: 0.8;">XP Reward</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #ffc107;">Pro</div>
                            <div style="font-size: 0.8rem; opacity: 0.8;">Difficulty</div>
                        </div>
                        <div>
                            <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">90m</div>
                            <div style="font-size: 0.8rem; opacity: 0.8;">Duration</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php if ($tutorial_completed): ?>
    <div class="completion-status">
        <div class="completion-icon">🎉</div>
        <div class="completion-text">
            You've already completed this tutorial! You can review the content, but no additional XP will be awarded.
        </div>
    </div>
    <?php endif; ?>

    <div class="progression-indicator">
        <div class="progress-step completed">1</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">2</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">3</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">4</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">5</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">6</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">7</div>
        <div class="progress-line completed"></div>
        <div class="progress-step completed">8</div>
    </div>

    <div class="step-card" data-step="1" style="--step-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="step-header">
            <div class="step-number">1</div>
            <div class="step-info">
                <h3>Understanding DDoS Attack Vectors</h3>
                <p>Comprehensive analysis of volumetric, protocol, and application-layer attack methodologies</p>
            </div>
        </div>

        <div class="step-content">
            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-analytics"></i> Attack Vector Classification
                </div>
                <div class="concept-content">
                    <p>DDoS attacks are categorized into three primary vectors, each targeting different layers of the network stack:</p>
                    <ul>
                        <li><strong>Volumetric Attacks:</strong> Target bandwidth consumption (Layer 3/4)</li>
                        <li><strong>Protocol Attacks:</strong> Exploit protocol weaknesses (Layer 3/4)</li>
                        <li><strong>Application Layer:</strong> Target specific applications (Layer 7)</li>
                    </ul>
                </div>
            </div>

            <div class="attack-demo">
                <div class="demo-title">
                    <i class="fas fa-exclamation-triangle"></i> Real-World Attack Analysis
                </div>
                <p><strong>Case Study:</strong> DNS Amplification Attack</p>
                <div class="code-block">
                    <div class="code-header">Attack Traffic Analysis</div>
                    <div class="command-line">$ tcpdump -i eth0 -n "udp port 53" | head -10</div>
                    <div>12:34:56.789 192.168.1.100.54321 > 8.8.8.8.53: DNS query (spoofed source)</div>
                    <div>12:34:56.790 8.8.8.8.53 > victim.com.80: DNS response (3000 bytes)</div>
                    <div>12:34:56.791 192.168.1.101.12345 > 1.1.1.1.53: DNS query (spoofed source)</div>
                    <div>12:34:56.792 1.1.1.1.53 > victim.com.80: DNS response (3000 bytes)</div>
                    <div class="comment-line"># Amplification ratio: 60 byte query -> 3000 byte response (50:1 ratio)</div>
                </div>
                <p>This attack demonstrates how attackers leverage open DNS resolvers to amplify small queries into massive responses directed at the victim.</p>
            </div>

            <div class="implementation-grid">
                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-wave-square"></i> Volumetric Indicators
                    </div>
                    <ul class="technique-list">
                        <li>Bandwidth utilization exceeding 80% of capacity</li>
                        <li>Packets per second (PPS) rates above baseline</li>
                        <li>Uniform packet sizes indicating bot traffic</li>
                        <li>Geographic distribution anomalies</li>
                    </ul>
                </div>

                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-network-wired"></i> Protocol Anomalies
                    </div>
                    <ul class="technique-list">
                        <li>Half-open connection floods (SYN floods)</li>
                        <li>Malformed packet structures</li>
                        <li>Fragmentation attacks bypassing filters</li>
                        <li>State table exhaustion patterns</li>
                    </ul>
                </div>

                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-globe"></i> Application Signatures
                    </div>
                    <ul class="technique-list">
                        <li>HTTP request pattern uniformity</li>
                        <li>User-Agent string clustering</li>
                        <li>Session behavior anomalies</li>
                        <li>Resource consumption spikes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="2" style="--step-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="step-header">
            <div class="step-number">2</div>
            <div class="step-info">
                <h3>Advanced Detection Methodologies</h3>
                <p>Implement statistical analysis and machine learning-based detection systems</p>
            </div>
        </div>

        <div class="step-content">
            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-chart-line"></i> Statistical Baseline Establishment
                </div>
                <p>Effective DDoS detection requires establishing statistical baselines for normal traffic patterns:</p>
                <div class="code-block">
                    <div class="code-header">Baseline Calculation Script</div>
                    <div class="command-line">#!/bin/bash</div>
                    <div class="comment-line"># Calculate 7-day moving average for traffic patterns</div>
                    <div>INTERFACE="eth0"</div>
                    <div>WINDOW=7</div>
                    <div></div>
                    <div class="command-line">for i in {1..168}; do  # 7 days * 24 hours</div>
                    <div>    PPS=$(cat /proc/net/dev | grep $INTERFACE | awk '{print $2}' | tail -1)</div>
                    <div>    BPS=$(cat /proc/net/dev | grep $INTERFACE | awk '{print $10}' | tail -1)</div>
                    <div>    echo "$(date '+%Y-%m-%d %H:%M:%S'),$PPS,$BPS" >> baseline.csv</div>
                    <div>    sleep 3600  # 1 hour intervals</div>
                    <div class="command-line">done</div>
                </div>
            </div>

            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-brain"></i> Entropy-Based Detection
                </div>
                <div class="concept-content">
                    <p>Entropy analysis helps detect subtle changes in traffic patterns that traditional threshold-based systems miss:</p>
                    <ul>
                        <li><strong>Source IP Entropy:</strong> Measures randomness in source IP distribution</li>
                        <li><strong>Packet Size Entropy:</strong> Detects uniform packet sizes from botnets</li>
                        <li><strong>Inter-arrival Time Entropy:</strong> Identifies synthetic traffic patterns</li>
                        <li><strong>Protocol Mix Entropy:</strong> Monitors protocol distribution changes</li>
                    </ul>
                </div>
            </div>

            <div class="defense-strategy">
                <div class="strategy-title">
                    <i class="fas fa-cogs"></i> Multi-Vector Detection Implementation
                </div>
                <div class="code-block">
                    <div class="code-header">Detection Algorithm (Python)</div>
                    <div class="config-line">import numpy as np</div>
                    <div class="config-line">from scipy import stats</div>
                    <div></div>
                    <div class="command-line">def detect_ddos_anomaly(current_metrics, baseline_metrics):</div>
                    <div>    """</div>
                    <div>    Multi-dimensional anomaly detection using statistical analysis</div>
                    <div>    """</div>
                    <div>    pps_zscore = abs(stats.zscore([current_metrics['pps']], baseline_metrics['pps']))</div>
                    <div>    entropy_deviation = abs(current_metrics['entropy'] - baseline_metrics['entropy_mean'])</div>
                    <div>    </div>
                    <div>    <span class="comment-line"># Composite anomaly score</span></div>
                    <div>    anomaly_score = (pps_zscore * 0.4) + (entropy_deviation * 0.6)</div>
                    <div>    </div>
                    <div>    return anomaly_score > THRESHOLD</div>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="3" style="--step-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="step-header">
            <div class="step-number">3</div>
            <div class="step-info">
                <h3>BGP-Based Mitigation Techniques</h3>
                <p>Implement Border Gateway Protocol solutions for upstream traffic filtering</p>
            </div>
        </div>

        <div class="step-content">
            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-route"></i> BGP Blackholing Implementation
                </div>
                <p>BGP blackholing allows you to instruct upstream providers to drop traffic destined for specific prefixes:</p>
                <div class="code-block">
                    <div class="code-header">Cisco BGP Configuration</div>
                    <div class="config-line">router bgp 65001</div>
                    <div class="config-line"> neighbor 203.0.113.1 remote-as 65000</div>
                    <div class="config-line"> neighbor 203.0.113.1 send-community</div>
                    <div></div>
                    <div class="comment-line">! Define blackhole community</div>
                    <div class="config-line">ip community-list 100 permit 65000:666</div>
                    <div></div>
                    <div class="comment-line">! Route-map for blackholing</div>
                    <div class="config-line">route-map BLACKHOLE permit 10</div>
                    <div class="config-line"> match community 100</div>
                    <div class="config-line"> set community 65535:666 additive</div>
                    <div></div>
                    <div class="comment-line">! Apply during attack</div>
                    <div class="config-line">ip route 192.0.2.100 255.255.255.255 null0</div>
                    <div class="config-line">network 192.0.2.100 mask 255.255.255.255</div>
                </div>
            </div>

            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-filter"></i> FlowSpec (RFC 5575) Implementation
                </div>
                <div class="concept-content">
                    <p>FlowSpec enables granular traffic filtering rules distributed via BGP:</p>
                    <div class="code-block">
                        <div class="code-header">FlowSpec Rule Example</div>
                        <div class="config-line">flow-route 1 match destination 192.0.2.0/24</div>
                        <div class="config-line"> match protocol udp</div>
                        <div class="config-line"> match destination-port 53</div>
                        <div class="config-line"> match packet-length >1024</div>
                        <div class="config-line"> then rate-limit 1000000</div>
                    </div>
                    <p>This rule rate-limits UDP traffic to port 53 (DNS) with packet sizes over 1024 bytes, effectively mitigating DNS amplification attacks.</p>
                </div>
            </div>

            <div class="implementation-grid">
                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-ban"></i> Blackhole Benefits
                    </div>
                    <ul class="technique-list">
                        <li>Immediate upstream traffic dropping</li>
                        <li>Preserves downstream bandwidth</li>
                        <li>Automatic propagation to providers</li>
                        <li>Minimal configuration overhead</li>
                    </ul>
                </div>

                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-sliders-h"></i> FlowSpec Advantages
                    </div>
                    <ul class="technique-list">
                        <li>Granular traffic filtering rules</li>
                        <li>Surgical traffic manipulation</li>
                        <li>Distributed rule enforcement</li>
                        <li>Real-time rule deployment</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="4" style="--step-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="step-header">
            <div class="step-number">4</div>
            <div class="step-info">
                <h3>Scrubbing Center Architecture</h3>
                <p>Design and deploy distributed traffic cleaning infrastructure</p>
            </div>
        </div>

        <div class="step-content">
            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-building"></i> Scrubbing Center Design Principles
                </div>
                <div class="concept-content">
                    <p>Enterprise scrubbing centers require careful architectural planning:</p>
                    <ul>
                        <li><strong>Geographic Distribution:</strong> Multiple sites for latency optimization</li>
                        <li><strong>Anycast Routing:</strong> Automatic traffic steering to nearest facility</li>
                        <li><strong>Capacity Planning:</strong> 10x normal traffic capacity minimum</li>
                        <li><strong>Clean Path Redundancy:</strong> Multiple clean traffic return paths</li>
                    </ul>
                </div>
            </div>

            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-project-diagram"></i> Traffic Flow Architecture
                </div>
                <div class="code-block">
                    <div class="code-header">Scrubbing Center Flow</div>
                    <div class="comment-line"># Normal Traffic Flow</div>
                    <div>Internet → Border Router → Core Network → Servers</div>
                    <div></div>
                    <div class="comment-line"># During Attack (BGP Redirection)</div>
                    <div>Internet → Border Router → Scrubbing Center → Analysis Engine</div>
                    <div>         ↓</div>
                    <div>Clean Traffic → GRE Tunnel → Core Network → Servers</div>
                    <div>Malicious Traffic → /dev/null</div>
                </div>
            </div>

            <div class="attack-demo">
                <div class="demo-title">
                    <i class="fas fa-cogs"></i> Automated Scrubbing Workflow
                </div>
                <div class="code-block">
                    <div class="code-header">Scrubbing Center Automation</div>
                    <div class="command-line">#!/bin/bash</div>
                    <div class="comment-line"># Automated DDoS response workflow</div>
                    <div></div>
                    <div class="command-line">detect_attack() {</div>
                    <div>    THRESHOLD=100000  # 100k PPS threshold</div>
                    <div>    CURRENT_PPS=$(get_current_pps)</div>
                    <div>    </div>
                    <div>    if [ $CURRENT_PPS -gt $THRESHOLD ]; then</div>
                    <div>        echo "Attack detected: ${CURRENT_PPS} PPS"</div>
                    <div>        redirect_to_scrubbing</div>
                    <div>        apply_mitigation_rules</div>
                    <div>        monitor_effectiveness</div>
                    <div>    fi</div>
                    <div class="command-line">}</div>
                </div>
            </div>

            <div class="implementation-grid">
                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-filter"></i> Deep Packet Inspection
                    </div>
                    <ul class="technique-list">
                        <li>Protocol conformance validation</li>
                        <li>Payload pattern analysis</li>
                        <li>Behavioral fingerprinting</li>
                        <li>Real-time signature matching</li>
                    </ul>
                </div>

                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-tachometer-alt"></i> Performance Metrics
                    </div>
                    <ul class="technique-list">
                        <li>Scrubbing latency < 5ms added</li>
                        <li>Clean traffic accuracy > 99.9%</li>
                        <li>False positive rate < 0.01%</li>
                        <li>Mitigation time < 60 seconds</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="5" style="--step-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
        <div class="step-header">
            <div class="step-number">5</div>
            <div class="step-info">
                <h3>Rate Limiting & Traffic Shaping</h3>
                <p>Implement advanced rate limiting using token bucket and leaky bucket algorithms</p>
            </div>
        </div>

        <div class="step-content">
            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-hourglass-half"></i> Token Bucket Algorithm Implementation
                </div>
                <p>Token bucket algorithms provide burst tolerance while maintaining average rate limits:</p>
                <div class="code-block">
                    <div class="code-header">Token Bucket Implementation (C++)</div>
                    <div class="config-line">class TokenBucket {</div>
                    <div class="config-line">private:</div>
                    <div>    int capacity;          // Maximum tokens</div>
                    <div>    int tokens;            // Current tokens</div>
                    <div>    int refill_rate;       // Tokens per second</div>
                    <div>    std::chrono::time_point last_refill;</div>
                    <div></div>
                    <div class="config-line">public:</div>
                    <div>    bool consume(int requested_tokens) {</div>
                    <div>        refill();  // Add tokens based on elapsed time</div>
                    <div>        </div>
                    <div>        if (tokens >= requested_tokens) {</div>
                    <div>            tokens -= requested_tokens;</div>
                    <div>            return true;  // Allow packet</div>
                    <div>        }</div>
                    <div>        return false;     // Drop packet</div>
                    <div>    }</div>
                    <div class="config-line">};</div>
                </div>
            </div>

            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-layer-group"></i> Hierarchical Rate Limiting
                </div>
                <div class="concept-content">
                    <p>Implement multi-tier rate limiting for comprehensive protection:</p>
                    <ul>
                        <li><strong>Global Limits:</strong> Overall bandwidth constraints</li>
                        <li><strong>Per-Source Limits:</strong> Individual IP rate limits</li>
                        <li><strong>Per-Service Limits:</strong> Application-specific limits</li>
                        <li><strong>Per-User Limits:</strong> Authenticated user quotas</li>
                    </ul>
                </div>
            </div>

            <div class="defense-strategy">
                <div class="strategy-title">
                    <i class="fas fa-shield-virus"></i> Adaptive Rate Limiting
                </div>
                <div class="code-block">
                    <div class="code-header">Linux TC (Traffic Control) Configuration</div>
                    <div class="command-line"># Create HTB (Hierarchical Token Bucket) qdisc</div>
                    <div class="config-line">tc qdisc add dev eth0 root handle 1: htb default 30</div>
                    <div></div>
                    <div class="command-line"># Root class - total bandwidth</div>
                    <div class="config-line">tc class add dev eth0 parent 1: classid 1:1 htb rate 1gbit</div>
                    <div></div>
                    <div class="command-line"># Web traffic class</div>
                    <div class="config-line">tc class add dev eth0 parent 1:1 classid 1:10 htb rate 800mbit ceil 900mbit</div>
                    <div></div>
                    <div class="command-line"># Emergency traffic class</div>
                    <div class="config-line">tc class add dev eth0 parent 1:1 classid 1:20 htb rate 100mbit ceil 200mbit</div>
                    <div></div>
                    <div class="command-line"># Apply filters</div>
                    <div class="config-line">tc filter add dev eth0 protocol ip parent 1:0 prio 1 u32 match ip dport 80 0xffff flowid 1:10</div>
                    <div class="config-line">tc filter add dev eth0 protocol ip parent 1:0 prio 1 u32 match ip dport 443 0xffff flowid 1:10</div>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="6" style="--step-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="step-header">
            <div class="step-number">6</div>
            <div class="step-info">
                <h3>Application Layer Protection</h3>
                <p>Deploy Web Application Firewalls and behavioral analysis systems</p>
            </div>
        </div>

        <div class="step-content">
            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-globe"></i> Layer 7 Attack Characteristics
                </div>
                <div class="concept-content">
                    <p>Application layer attacks require sophisticated detection and mitigation:</p>
                    <ul>
                        <li><strong>HTTP Floods:</strong> High request rates targeting web servers</li>
                        <li><strong>Slowloris:</strong> Low-bandwidth attacks keeping connections open</li>
                        <li><strong>API Abuse:</strong> Targeting expensive API endpoints</li>
                        <li><strong>Cache Busting:</strong> Bypassing caching mechanisms</li>
                    </ul>
                </div>
            </div>

            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-shield-check"></i> ModSecurity WAF Configuration
                </div>
                <div class="code-block">
                    <div class="code-header">ModSecurity DDoS Rules</div>
                    <div class="comment-line"># Rate limiting rule for excessive requests</div>
                    <div class="config-line">SecRule REQUEST_HEADERS:User-Agent "@detectSQLi" \</div>
                    <div>    "id:1001,\</div>
                    <div>     phase:1,\</div>
                    <div>     block,\</div>
                    <div>     msg:'SQL Injection Attack Detected',\</div>
                    <div>     logdata:'Matched Data: %{MATCHED_VAR} found within %{MATCHED_VAR_NAME}'"</div>
                    <div></div>
                    <div class="comment-line"># Rate limiting by IP</div>
                    <div class="config-line">SecAction \</div>
                    <div>    "id:1002,\</div>
                    <div>     phase:1,\</div>
                    <div>     nolog,\</div>
                    <div>     pass,\</div>
                    <div>     initcol:ip=%{REMOTE_ADDR},\</div>
                    <div>     setvar:ip.requests_per_minute=+1,\</div>
                    <div>     expirevar:ip.requests_per_minute=60"</div>
                    <div></div>
                    <div class="config-line">SecRule IP:REQUESTS_PER_MINUTE "@gt 60" \</div>
                    <div>    "id:1003,\</div>
                    <div>     phase:1,\</div>
                    <div>     block,\</div>
                    <div>     msg:'Client exceeded 60 requests per minute'"</div>
                </div>
            </div>

            <div class="attack-demo">
                <div class="demo-title">
                    <i class="fas fa-robot"></i> Bot Detection Implementation
                </div>
                <div class="code-block">
                    <div class="code-header">JavaScript Challenge (Anti-Bot)</div>
                    <div class="config-line">&lt;script&gt;</div>
                    <div class="comment-line">// Challenge-response mechanism</div>
                    <div>function generateChallenge() {</div>
                    <div>    const timestamp = Date.now();</div>
                    <div>    const random = Math.random().toString(36);</div>
                    <div>    const challenge = btoa(timestamp + ':' + random);</div>
                    <div>    </div>
                    <div>    // Solve computational challenge</div>
                    <div>    const solution = sha256(challenge + 'secret_salt');</div>
                    <div>    </div>
                    <div>    // Submit solution with request</div>
                    <div>    fetch('/api/verify', {</div>
                    <div>        headers: { 'X-Challenge-Solution': solution }</div>
                    <div>    });</div>
                    <div>}</div>
                    <div class="config-line">&lt;/script&gt;</div>
                </div>
            </div>

            <div class="implementation-grid">
                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-search"></i> Behavioral Analysis
                    </div>
                    <ul class="technique-list">
                        <li>Request timing pattern analysis</li>
                        <li>Session duration monitoring</li>
                        <li>Mouse movement tracking</li>
                        <li>Browser fingerprinting</li>
                    </ul>
                </div>

                <div class="implementation-card">
                    <div class="card-header">
                        <i class="fas fa-puzzle-piece"></i> CAPTCHA Integration
                    </div>
                    <ul class="technique-list">
                        <li>Progressive challenge escalation</li>
                        <li>Risk-based challenge triggers</li>
                        <li>Accessibility-compliant alternatives</li>
                        <li>Machine learning validation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="7" style="--step-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="step-header">
            <div class="step-number">7</div>
            <div class="step-info">
                <h3>Incident Response & Coordination</h3>
                <p>Establish automated response procedures and communication protocols</p>
            </div>
        </div>

        <div class="step-content">
            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-sitemap"></i> Response Team Structure
                </div>
                <div class="concept-content">
                    <p>Effective DDoS response requires coordinated team efforts:</p>
                    <ul>
                        <li><strong>Incident Commander:</strong> Overall coordination and decision making</li>
                                                <li><strong>Network Engineer:</strong> BGP routing and infrastructure changes</li>
                        <li><strong>Security Analyst:</strong> Attack analysis and signature development</li>
                        <li><strong>Communications Lead:</strong> Stakeholder updates and external coordination</li>
                    </ul>
                </div>
            </div>

            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-bell"></i> Automated Alert System
                </div>
                <div class="code-block">
                    <div class="code-header">Incident Response Automation</div>
                    <div class="command-line">#!/bin/bash</div>
                    <div class="comment-line"># DDoS incident response automation script</div>
                    <div></div>
                    <div class="command-line">declare_incident() {</div>
                    <div>    SEVERITY=$1</div>
                    <div>    ATTACK_TYPE=$2</div>
                    <div>    TARGET_IP=$3</div>
                    <div></div>
                    <div>    <span class="comment-line"># Generate incident ID</span></div>
                    <div>    INCIDENT_ID="DDOS-$(date +%Y%m%d-%H%M%S)"</div>
                    <div></div>
                    <div>    <span class="comment-line"># Alert security team</span></div>
                    <div>    curl -X POST "https://api.pagerduty.com/incidents" \</div>
                    <div>         -H "Authorization: Token ${PD_TOKEN}" \</div>
                    <div>         -d "{\"incident\":{\"type\":\"incident\",\"title\":\"DDoS Attack - ${SEVERITY}\"}}"</div>
                    <div></div>
                    <div>    <span class="comment-line"># Update status page</span></div>
                    <div>    update_status_page "${INCIDENT_ID}" "investigating"</div>
                    <div></div>
                    <div>    <span class="comment-line"># Initiate automated mitigation</span></div>
                    <div>    case $ATTACK_TYPE in</div>
                    <div>        "volumetric")</div>
                    <div>            enable_upstream_filtering $TARGET_IP</div>
                    <div>            ;;</div>
                    <div>        "application")</div>
                    <div>            activate_waf_rules $TARGET_IP</div>
                    <div>            ;;</div>
                    <div>    esac</div>
                    <div class="command-line">}</div>
                </div>
            </div>

            <div class="defense-strategy">
                <div class="strategy-title">
                    <i class="fas fa-clipboard-list"></i> Response Playbook
                </div>
                <div style="display: grid; gap: 1rem;">
                    <div style="background: rgba(0, 0, 0, 0.3); border-radius: 8px; padding: 1rem;">
                        <strong style="color: #ff6b6b;">Phase 1: Detection (0-2 minutes)</strong>
                        <ul style="margin: 0.5rem 0; color: var(--text-secondary);">
                            <li>Automated monitoring triggers alert</li>
                            <li>Initial triage and attack vector identification</li>
                            <li>Escalation to on-call security engineer</li>
                        </ul>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.3); border-radius: 8px; padding: 1rem;">
                        <strong style="color: #ffc107;">Phase 2: Analysis (2-5 minutes)</strong>
                        <ul style="margin: 0.5rem 0; color: var(--text-secondary);">
                            <li>Traffic pattern analysis and attack classification</li>
                            <li>Impact assessment and affected services identification</li>
                            <li>Mitigation strategy selection</li>
                        </ul>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.3); border-radius: 8px; padding: 1rem;">
                        <strong style="color: #4facfe;">Phase 3: Mitigation (5-15 minutes)</strong>
                        <ul style="margin: 0.5rem 0; color: var(--text-secondary);">
                            <li>Deploy appropriate countermeasures</li>
                            <li>Monitor mitigation effectiveness</li>
                            <li>Adjust filtering rules as needed</li>
                        </ul>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.3); border-radius: 8px; padding: 1rem;">
                        <strong style="color: #00b894;">Phase 4: Recovery (15+ minutes)</strong>
                        <ul style="margin: 0.5rem 0; color: var(--text-secondary);">
                            <li>Verify service restoration</li>
                            <li>Gradual mitigation rule relaxation</li>
                            <li>Post-incident documentation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="step-card" data-step="8" style="--step-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="step-header">
            <div class="step-number">8</div>
            <div class="step-info">
                <h3>Post-Attack Analysis & Improvement</h3>
                <p>Conduct forensic analysis and enhance defense capabilities</p>
            </div>
        </div>

        <div class="step-content">
            <div class="technical-details">
                <div class="technical-title">
                    <i class="fas fa-search-plus"></i> Attack Forensics & Attribution
                </div>
                <div class="code-block">
                    <div class="code-header">Log Analysis Script</div>
                    <div class="command-line">#!/usr/bin/python3</div>
                    <div class="comment-line"># DDoS attack forensics analyzer</div>
                    <div></div>
                    <div class="config-line">import pandas as pd</div>
                    <div class="config-line">import matplotlib.pyplot as plt</div>
                    <div class="config-line">from collections import Counter</div>
                    <div></div>
                    <div class="command-line">def analyze_attack_patterns(log_file):</div>
                    <div>    """Analyze DDoS attack patterns from network logs"""</div>
                    <div>    df = pd.read_csv(log_file)</div>
                    <div>    </div>
                    <div>    <span class="comment-line"># Source IP distribution analysis</span></div>
                    <div>    source_ips = Counter(df['src_ip'])</div>
                    <div>    top_sources = source_ips.most_common(20)</div>
                    <div>    </div>
                    <div>    <span class="comment-line"># Geographic distribution</span></div>
                    <div>    geo_data = get_ip_geolocation(top_sources)</div>
                    <div>    </div>
                    <div>    <span class="comment-line"># Attack vector identification</span></div>
                    <div>    protocol_dist = df['protocol'].value_counts()</div>
                    <div>    port_dist = df['dst_port'].value_counts()</div>
                    <div>    </div>
                    <div>    <span class="comment-line"># Generate forensics report</span></div>
                    <div>    generate_forensics_report(top_sources, geo_data, protocol_dist)</div>
                </div>
            </div>

            <div class="concept-box">
                <div class="concept-title">
                    <i class="fas fa-chart-bar"></i> Defense Effectiveness Metrics
                </div>
                <div class="concept-content">
                    <p>Measure and improve your DDoS defense capabilities:</p>
                    <ul>
                        <li><strong>Detection Time:</strong> Time from attack start to alert generation</li>
                        <li><strong>Mitigation Time:</strong> Time from detection to effective countermeasures</li>
                        <li><strong>False Positive Rate:</strong> Percentage of legitimate traffic blocked</li>
                        <li><strong>Attack Success Rate:</strong> Percentage of attacks causing service degradation</li>
                    </ul>
                </div>
            </div>

            <div class="defense-strategy">
                <div class="strategy-title">
                    <i class="fas fa-cogs"></i> Continuous Improvement Process
                </div>
                <div class="implementation-grid">
                    <div class="implementation-card">
                        <div class="card-header">
                            <i class="fas fa-flask"></i> Testing & Validation
                        </div>
                        <ul class="technique-list">
                            <li>Regular red team exercises</li>
                            <li>Controlled attack simulations</li>
                            <li>Mitigation system stress testing</li>
                            <li>Failover scenario validation</li>
                        </ul>
                    </div>

                    <div class="implementation-card">
                        <div class="card-header">
                            <i class="fas fa-sync-alt"></i> Process Refinement
                        </div>
                        <ul class="technique-list">
                            <li>Playbook updates based on incidents</li>
                            <li>Detection threshold optimization</li>
                            <li>Team training and certification</li>
                            <li>Technology stack evaluation</li>
                        </ul>
                    </div>

                    <div class="implementation-card">
                        <div class="card-header">
                            <i class="fas fa-share-alt"></i> Threat Intelligence
                        </div>
                        <ul class="technique-list">
                            <li>IOC sharing with industry peers</li>
                            <li>Threat feed integration</li>
                            <li>Attack signature development</li>
                            <li>Proactive defense updates</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="attack-demo">
                <div class="demo-title">
                    <i class="fas fa-graduation-cap"></i> Lessons Learned Documentation
                </div>
                <p>Document key findings for future reference:</p>
                <div class="code-block">
                    <div class="code-header">Incident Report Template</div>
                    <div class="comment-line"># DDoS Incident Report - DDOS-20250920-070416</div>
                    <div></div>
                    <div><strong>Attack Summary:</strong></div>
                    <div>- Attack Type: DNS Amplification</div>
                    <div>- Peak Traffic: 2.5 Gbps</div>
                    <div>- Duration: 45 minutes</div>
                    <div>- Affected Services: Web application</div>
                    <div></div>
                    <div><strong>Response Timeline:</strong></div>
                    <div>- Detection: 2 minutes</div>
                    <div>- Mitigation: 8 minutes</div>
                    <div>- Full Recovery: 15 minutes</div>
                    <div></div>
                    <div><strong>Improvements Identified:</strong></div>
                    <div>- Reduce detection threshold for DNS traffic</div>
                    <div>- Implement automated BGP blackholing</div>
                    <div>- Enhance upstream provider coordination</div>
                </div>
            </div>
        </div>
    </div>

    <div class="quiz-section">
        <h2 class="quiz-title">Advanced DDoS Defense Assessment</h2>
        <p class="quiz-description">
            Test your mastery of advanced DDoS defense concepts. Questions are randomized and cover enterprise-grade mitigation strategies.
        </p>
        
        <form id="quizForm">
            <?php foreach ($randomized_questions as $index => $question): ?>
            <div class="quiz-question" data-question-id="<?= htmlspecialchars($question['id']) ?>">
                <div class="question-header">
                    <div class="question-number"><?= $index + 1 ?></div>
                    <div class="question-text"><?= htmlspecialchars($question['question']) ?></div>
                </div>
                
                <div class="question-options">
                    <?php foreach ($question['options'] as $option_index => $option): ?>
                    <div class="option" data-option="<?= $option_index ?>" data-correct="<?= $question['correct'] ?>">
                        <div class="option-letter"><?= chr(65 + $option_index) ?></div>
                        <div class="option-text"><?= htmlspecialchars($option) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="explanation">
                    <div class="explanation-title">
                        <i class="fas fa-lightbulb"></i> Expert Explanation
                    </div>
                    <div class="explanation-text"><?= htmlspecialchars($question['explanation']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="quiz-controls">
                <button type="button" id="submitQuiz" class="quiz-btn">
                    <i class="fas fa-check"></i> Submit Assessment
                </button>
                <button type="button" id="retakeQuiz" class="quiz-btn" style="display: none;">
                    <i class="fas fa-redo"></i> Retake Assessment
                </button>
            </div>
            
            <div class="quiz-results" id="quizResults">
                <div class="results-score" id="finalScore">0%</div>
                <div class="results-message" id="resultsMessage"></div>
                <?php if (!$tutorial_completed): ?>
                <button type="button" id="completeTutorial" class="quiz-btn" style="display: none;">
                    <i class="fas fa-trophy"></i> Complete Tutorial (+150 XP)
                </button>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="navigation">
        <a href="https://learnit.systems/tutorials/index.php" class="nav-btn">🔙 Back to Tutorials</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressSteps = document.querySelectorAll('.progress-step');
    const progressLines = document.querySelectorAll('.progress-line');
    const stepCards = document.querySelectorAll('.step-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const stepNumber = parseInt(entry.target.getAttribute('data-step'));
                updateProgress(stepNumber);
            }
        });
    }, { threshold: 0.3 });

    stepCards.forEach(card => observer.observe(card));

    function updateProgress(currentStep) {
        progressSteps.forEach((step, index) => {
            const stepNum = index + 1;
            if (stepNum < currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
            } else if (stepNum === currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('completed', 'active');
            }
        });

        progressLines.forEach((line, index) => {
            if (index + 1 < currentStep) {
                line.classList.add('completed');
            } else {
                line.classList.remove('completed');
            }
        });
    }

    const quizForm = document.getElementById('quizForm');
    const options = document.querySelectorAll('.option');
    const submitBtn = document.getElementById('submitQuiz');
    const retakeBtn = document.getElementById('retakeQuiz');
    const completeBtn = document.getElementById('completeTutorial');
    const resultsDiv = document.getElementById('quizResults');
    const scoreDisplay = document.getElementById('finalScore');
    const messageDisplay = document.getElementById('resultsMessage');
    
    let selectedAnswers = {};
    let quizSubmitted = false;
    
    options.forEach(option => {
        option.addEventListener('click', function() {
            if (quizSubmitted) return;
            
            const questionDiv = this.closest('.quiz-question');
            const questionId = questionDiv.getAttribute('data-question-id');
            const optionValue = this.getAttribute('data-option');
            
            questionDiv.querySelectorAll('.option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            this.classList.add('selected');
            selectedAnswers[questionId] = parseInt(optionValue);
            
            updateSubmitButton();
        });
    });
    
    function updateSubmitButton() {
        const totalQuestions = document.querySelectorAll('.quiz-question').length;
        const answeredQuestions = Object.keys(selectedAnswers).length;
        
        submitBtn.disabled = answeredQuestions < totalQuestions;
    }
    
    submitBtn.addEventListener('click', function() {
        if (quizSubmitted) return;
        
        quizSubmitted = true;
        let correctAnswers = 0;
        const totalQuestions = document.querySelectorAll('.quiz-question').length;
        
        document.querySelectorAll('.quiz-question').forEach(questionDiv => {
            const questionId = questionDiv.getAttribute('data-question-id');
            const selectedOption = selectedAnswers[questionId];
            const options = questionDiv.querySelectorAll('.option');
            const explanation = questionDiv.querySelector('.explanation');
            
            options.forEach(option => {
                const optionIndex = parseInt(option.getAttribute('data-option'));
                const correctIndex = parseInt(option.getAttribute('data-correct'));
                
                if (optionIndex === correctIndex) {
                    option.classList.add('correct');
                    if (selectedOption === correctIndex) {
                        correctAnswers++;
                    }
                } else if (optionIndex === selectedOption) {
                    option.classList.add('incorrect');
                }
                
                option.style.pointerEvents = 'none';
            });
            
            explanation.classList.add('show');
        });
        
        const scorePercentage = Math.round((correctAnswers / totalQuestions) * 100);
        scoreDisplay.textContent = scorePercentage + '%';
        
        let message = '';
        if (scorePercentage >= 85) {
            message = `Outstanding mastery! You scored ${correctAnswers}/${totalQuestions} correct. You have achieved expert-level understanding of advanced DDoS defense strategies and are ready to lead enterprise security initiatives.`;
            if (completeBtn && !<?= $tutorial_completed ? 'true' : 'false' ?>) {
                completeBtn.style.display = 'inline-block';
            }
        } else if (scorePercentage >= 70) {
            message = `Strong performance! You scored ${correctAnswers}/${totalQuestions} correct. You demonstrate solid understanding of DDoS defense concepts. Review the advanced topics to achieve expert level.`;
        } else {
            message = `You scored ${correctAnswers}/${totalQuestions} correct. Advanced DDoS defense requires deep technical knowledge. Study the step-by-step guide and retry the assessment.`;
        }
        
        messageDisplay.textContent = message;
        resultsDiv.classList.add('show');
        
        submitBtn.style.display = 'none';
        retakeBtn.style.display = 'inline-block';
        
        resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
    
    retakeBtn.addEventListener('click', function() {
        fetch('../api/clear-quiz-session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                tutorial_id: '<?= $tutorial_id ?>'
            })
        }).then(() => {
            location.reload();
        }).catch(() => {
            location.reload();
        });
    });
    
    if (completeBtn) {
        completeBtn.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Completing...';
            
            fetch('../api/complete-tutorial.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    tutorial_id: '<?= $tutorial_id ?>',
                    user_id: <?= $user_id ?>,
                    username: '<?= htmlspecialchars($username) ?>',
                    points: 150,
                    timestamp: '2025-09-20 07:04:16'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fas fa-check"></i> Tutorial Completed! (+150 XP)';
                    btn.style.background = 'linear-gradient(135deg, #00b894, #00cec9)';
                    
                    setTimeout(() => {
                        window.location.href = '../tutorials/';
                    }, 2000);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-trophy"></i> Complete Tutorial (+150 XP)';
                    alert('Error completing tutorial. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trophy"></i> Complete Tutorial (+150 XP)';
                alert('Error completing tutorial. Please try again.');
            });
        });
    }
    
    updateSubmitButton();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

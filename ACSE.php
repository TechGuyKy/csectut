<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('log_errors',1);
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';
if(!isLoggedIn()){
    header("Location: ../auth/login.php");
    exit;
}
$user=currentUser();
$user_id=$_SESSION['user_id']??0;
$username=$user['username']??'User';
$tutorial_id='advanced-cloud-security-exploits-v2';
$tutorial_completed=false;
if(function_exists('isTutorialCompleted')){
    $tutorial_completed=isTutorialCompleted($tutorial_id,$user_id);
}
$current_time=new DateTime('now',new DateTimeZone('UTC'));
$timestamp=$current_time->format('Y-m-d H:i:s');

$quiz_questions=[
    [
        'id'=>'q1',
        'question'=>'Which detection pattern most reliably reveals a chained cross-account role pivot in progress?',
        'options'=>[
            'Increase in CloudWatch metrics for network throughput',
            'Single region login followed by high frequency credential report calls',
            'First-seen external principal assuming escalating privilege roles with decreasing time gaps',
            'Abnormally large object listing operations without encryption'
        ],
        'correct'=>2,
        'explanation'=>'Progressively shorter intervals between increasingly privileged cross-account role assumptions is a distinctive pivot chain signature.'
    ],
    [
        'id'=>'q2',
        'question'=>'Which preventative control most reduces blast radius of metadata credential theft via SSRF?',
        'options'=>[
            'Disabling autoscaling groups',
            'Implementing hardened metadata service session tokens with enforced hop limits and outbound egress domain allow-list',
            'Using larger instance types',
            'Switching to regional object storage only'
        ],
        'correct'=>1,
        'explanation'=>'Session token enforced metadata plus granular outbound egress domain controls limits abuse of harvested credentials.'
    ],
    [
        'id'=>'q3',
        'question'=>'Which signal best indicates container breakout progression toward node credential harvesting?',
        'options'=>[
            'Spike in GET requests on public HTTP endpoints',
            'Read attempts on kubelet config and service account token files',
            'Increased CPU from image pulls',
            'Periodic health probe failures'
        ],
        'correct'=>1,
        'explanation'=>'Access to kubelet config or service account token paths strongly suggests credential targeting after escape attempt.'
    ],
    [
        'id'=>'q4',
        'question'=>'Which integrity gate offers earliest detection of artifact supply chain poisoning?',
        'options'=>[
            'Daily vulnerability scan report',
            'Runtime anomaly in service latency',
            'Pre-promotion cryptographic provenance and dependency graph hash verification',
            'Manual developer peer review only'
        ],
        'correct'=>2,
        'explanation'=>'Early pipeline-stage provenance and dependency graph hashing detect tampering before deployment.'
    ],
    [
        'id'=>'q5',
        'question'=>'Which abnormal tag pattern indicates covert persistence via metadata fields?',
        'options'=>[
            'Consistent lowercase resource tags',
            'Gradual entropy rise and irregular base64-like mutations in non-critical tag values',
            'Removal of deprecated tags only',
            'High number of cost center tags'
        ],
        'correct'=>1,
        'explanation'=>'Increasing entropy with evolving encoded patterns in benign tag fields indicates covert channel attempts.'
    ],
    [
        'id'=>'q6',
        'question'=>'What most effectively blocks privilege escalation via event trigger chaining?',
        'options'=>[
            'Increasing function timeout',
            'Restricting trigger principals and enforcing provenance validation on chained invocations',
            'Switching storage tiers',
            'Adding more logging retention'
        ],
        'correct'=>1,
        'explanation'=>'Trigger principal restriction plus provenance validation breaks chained privilege escalation paths.'
    ],
    [
        'id'=>'q7',
        'question'=>'Which lateral movement metric shows risk expansion across identity tiers?',
        'options'=>[
            'Total number of dashboards loaded',
            'Identity chain length growth with rising privilege overlap ratio',
            'Count of daily code commits',
            'Volume of billing API calls'
        ],
        'correct'=>1,
        'explanation'=>'Increased chain length and privilege overlap signals escalating lateral movement complexity.'
    ],
    [
        'id'=>'q8',
        'question'=>'Which mitigation best reduces ephemeral signed URL exfil staging risk?',
        'options'=>[
            'Increasing bucket versioning only',
            'Adaptive throttling and anomaly scoring of signed URL issuance with geo dispersion correlation',
            'Moving objects to infrequent access tier',
            'Longer URL validity durations'
        ],
        'correct'=>1,
        'explanation'=>'Adaptive throttling with geo dispersion correlation disrupts staged exfil patterns using signed URLs.'
    ],
    [
        'id'=>'q9',
        'question'=>'Which signal combination reveals supply chain artifact impersonation?',
        'options'=>[
            'Consistent build durations and unchanged dependencies',
            'Mismatched provenance signature plus unexpected dependency subtree addition',
            'Increased CPU load only',
            'Stable rollout speed'
        ],
        'correct'=>1,
        'explanation'=>'Signature mismatch combined with altered dependency subtree indicates impersonated artifact.'
    ],
    [
        'id'=>'q10',
        'question'=>'What primary defense reduces container escape lateral blast radius post-compromise?',
        'options'=>[
            'Higher node storage capacity',
            'Strict runtime profile enforcement and minimized node credential exposure',
            'Adding more cluster labels',
            'Consolidating workloads onto fewer nodes'
        ],
        'correct'=>1,
        'explanation'=>'Runtime profile enforcement and minimizing node credential exposure restrict lateral expansion.'
    ],
    [
        'id'=>'q11',
        'question'=>'Which metric best signals covert exfil staging readiness phase?',
        'options'=>[
            'Occasional console login',
            'Burst of short-lived credential or signed URL generation with rising regional dispersion',
            'Increase in DNS queries for internal zones only',
            'Stable object retrieval latency'
        ],
        'correct'=>1,
        'explanation'=>'Rapid issuance of short-lived access constructs with geographic dispersion indicates staging for exfil.'
    ],
    [
        'id'=>'q12',
        'question'=>'Which control directly interrupts multi-hop identity impersonation chains?',
        'options'=>[
            'More frequent cost allocation tagging',
            'Enforced hop length ceiling and privilege intersection minimization policies',
            'Increasing storage encryption replicas',
            'Extending session token lifetimes'
        ],
        'correct'=>1,
        'explanation'=>'Hop length ceilings and minimized privilege intersections reduce exploitability of impersonation chains.'
    ]
];

if(!isset($_SESSION['quiz_order_'.$tutorial_id])){
    $_SESSION['quiz_order_'.$tutorial_id]=range(0,count($quiz_questions)-1);
    shuffle($_SESSION['quiz_order_'.$tutorial_id]);
}
$randomized_questions=[];
foreach($_SESSION['quiz_order_'.$tutorial_id] as $i){
    $q=$quiz_questions[$i];
    if(!isset($_SESSION['option_order_'.$q['id']])){
        $opts=$q['options'];
        $idxs=range(0,count($opts)-1);
        shuffle($idxs);
        $new_opts=[];
        $new_correct=0;
        foreach($idxs as $new_index=>$old_index){
            $new_opts[]=$opts[$old_index];
            if($old_index===$q['correct'])$new_correct=$new_index;
        }
        $q['options']=$new_opts;
        $q['correct']=$new_correct;
        $_SESSION['option_order_'.$q['id']]=$q;
    } else {
        $q=$_SESSION['option_order_'.$q['id']];
    }
    $randomized_questions[]=$q;
}

$scenarios=[
    [
        'id'=>'identity-pivot',
        'title'=>'Identity Federation Role Pivot Chain',
        'phases'=>['Recon Baseline','External Role Assume','Enumeration Burst','Privilege Drift','Containment','Validation'],
        'terminal'=>[
            '>> baselining role assume patterns',
            'ok baseline distinct_principals=1 avg_interval=900s',
            '>> anomaly external principal appear',
            'alert assumeRole external-account detected privilege_tier=mid deviation=high',
            '>> enumeration burst sequence',
            'burst iam:ListRoles iam:GetRole iam:ListPolicies iam:GetUserPolicy window=4.1s score=8.7',
            '>> privilege drift trending upward',
            'drift calc new_actions=17 overlap_increase=23%',
            '>> containment sequence executing',
            'contain revoke_sessions=3 trust_patch=applied conditional_external_id=enabled',
            '>> verification stage complete residual_risk=low'
        ]
    ],
    [
        'id'=>'metadata-ssrf',
        'title'=>'Metadata Credential Exposure Attempt',
        'phases'=>['Baseline','Probe Pattern','Token Acquisition','Anomaly Correlated','Egress Lockdown','Residual Analysis'],
        'terminal'=>[
            '>> establishing baseline internal calls',
            'baseline metadata_calls_per_min=0 anomaly_score=0',
            '>> probe pattern deviation',
            'probe detected path=/latest/meta-data/iam/security-credentials/ variance=6.2',
            '>> temporary token observed ttl=900s',
            'temp credential usage burst serviceMismatch=true',
            '>> anomaly fusion triggered alert_id=md-442',
            'alert channel triaged severity=high',
            '>> egress lockdown initiated',
            'egress allow-list enforced restricted_domains=5',
            '>> residual risk scored score=0.31'
        ]
    ],
    [
        'id'=>'container-breakout',
        'title'=>'Container Breakout Drift Sequence',
        'phases'=>['Profile Baseline','Node Resource Probe','Sensitive Path Access','Drift Spike','Runtime Constrain','Post State'],
        'terminal'=>[
            '>> capturing baseline runtime profile',
            'baseline syscalls=82 variance=nominal',
            '>> probe attempt scanning host paths',
            'probe access /var/run/.tokens attempt flagged',
            '>> sensitive read attempt',
            'read /var/lib/kubelet/kubeconfig status=denied',
            '>> drift spike detected variance=34%',
            'profile anomaly set risk=raised',
            '>> applying runtime constrains',
            'enforce profile read_only_root=true seccomp=strict',
            '>> residual drift lowered to 7%'
        ]
    ],
    [
        'id'=>'artifact-supply',
        'title'=>'Artifact Supply Chain Integrity Divergence',
        'phases'=>['Stage Artifact','Signature Verify','Graph Audit','Provenance Divergence','Quarantine','Remediation'],
        'terminal'=>[
            '>> staging artifact svc-core-2025.9.21',
            'artifact hash=ae91... channel=staging',
            '>> signature verification',
            'signature mismatch expected=sha256:98fe actual=sha256:ab12',
            '>> dependency graph expansion',
            'graph anomaly new_subtree=crypto-ext unexpected=true',
            '>> provenance divergence flagged',
            'provenance_chain disrupted risk=critical',
            '>> quarantine engaged',
            'quarantine depth=1 propagation=halted',
            '>> remediation chain rebuilding status=ongoing'
        ]
    ]
];
?>
<style>
:root {
    --bg:#0b1016;
    --panel:#121a23;
    --panel-alt:#18222d;
    --panel-soft:#1e2a37;
    --accent:#04d2d2;
    --accent-soft:rgba(4,210,210,0.14);
    --accent-glow:0 0 0 1px rgba(4,210,210,0.25),0 0 18px -2px rgba(4,210,210,0.35);
    --focus:#ffb347;
    --focus-soft:rgba(255,179,71,0.16);
    --danger:#ff4d52;
    --danger-soft:rgba(255,77,82,0.15);
    --ok:#2bcf88;
    --ok-soft:rgba(43,207,136,0.16);
    --text:#f2f6fa;
    --text-dim:#8fa0b2;
    --border:#24313f;
    --radius-lg:26px;
    --radius-md:16px;
    --radius-sm:9px;
    --mono:'JetBrains Mono',monospace;
    --sans:'Inter',system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;
    --grad-accent:linear-gradient(135deg,#06eeee,#04b3b3);
    --grad-focus:linear-gradient(135deg,#ffd26f,#ff9d31);
    --grad-danger:linear-gradient(135deg,#ff6565,#d62831);
    --transition:.35s cubic-bezier(.4,0,.25,1);
}
body {
    background:radial-gradient(circle at 18% 12%,rgba(4,210,210,0.08),transparent 60%),radial-gradient(circle at 82% 78%,rgba(255,179,71,0.07),transparent 65%),#0b1016;
    color:var(--text);
    font-family:var(--sans);
    line-height:1.55;
}
.tutorial-hero {
    position:relative;
    padding:4.8rem 0 3.6rem;
    background:linear-gradient(160deg,#0e151c,#101a23 70%,#0e151c);
    margin:0 -15px 3.2rem;
    overflow:hidden;
}
.tutorial-hero:before {
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(145deg,rgba(4,210,210,0.18),transparent 55%),linear-gradient(35deg,rgba(255,179,71,0.16),transparent 60%);
    mix-blend-mode:overlay;
    opacity:.85;
    pointer-events:none;
}
.hero-inner {position:relative;z-index:2;max-width:1260px;margin:0 auto;}
.hero-badge {
    display:inline-flex;
    align-items:center;
    gap:.55rem;
    padding:.55rem 1.05rem;
    border:1px solid var(--border);
    border-radius:999px;
    background:var(--accent-soft);
    color:var(--accent);
    font-size:.7rem;
    letter-spacing:1.2px;
    font-weight:600;
    text-transform:uppercase;
}
.hero-title {
    font-size:clamp(2rem,4.2vw,3.65rem);
    line-height:1.05;
    margin:1.3rem 0 1.1rem;
    font-weight:700;
    letter-spacing:.5px;
    background:linear-gradient(90deg,#eef4fa,#b1c1cf);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}
.hero-sub {
    max-width:840px;
    font-size:1.02rem;
    color:var(--text-dim);
}
.metrics {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
    gap:1.1rem;
    margin-top:2.6rem;
    max-width:820px;
}
.metric {
    position:relative;
    background:linear-gradient(180deg,#121a23,#11181f);
    border:1px solid var(--border);
    border-radius:18px;
    padding:1.05rem 1.2rem 1.25rem;
    overflow:hidden;
    transition:var(--transition);
}
.metric:before {
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(135deg,rgba(4,210,210,0.12),transparent 70%);
    opacity:0;
    transition:var(--transition);
}
.metric:hover:before {opacity:1;}
.metric-value {
    font-size:1.7rem;
    font-weight:600;
    background:linear-gradient(120deg,#04d2d2,#2bcf88);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}
.metric-label {
    margin-top:.35rem;
    font-size:.68rem;
    letter-spacing:1.3px;
    font-weight:600;
    text-transform:uppercase;
    color:var(--text-dim);
}
.completion-status {
    background:var(--ok-soft);
    border:1px solid rgba(43,207,136,0.55);
    border-radius:18px;
    padding:1.8rem 1.6rem;
    text-align:center;
    margin:0 0 2.3rem;
}
.completion-status .icon {font-size:2.4rem;line-height:1;margin-bottom:.4rem;}
.progression-indicator {
    display:flex;
    flex-wrap:wrap;
    justify-content:center;
    gap:.85rem;
    padding:2rem 1.2rem;
    background:linear-gradient(180deg,#121a23,#121a23 55%,#10171f);
    border:1px solid var(--border);
    border-radius:26px;
    margin:2.8rem 0 3.6rem;
}
.progress-step {
    width:50px;
    height:50px;
    border-radius:15px;
    background:#141f28;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:600;
    font-size:.95rem;
    letter-spacing:.5px;
    color:var(--text-dim);
    border:1px solid var(--border);
    transition:var(--transition);
}
.progress-step.active {background:var(--accent-soft);color:var(--accent);border-color:var(--accent);box-shadow:var(--accent-glow);}
.progress-step.completed {background:var(--ok-soft);color:var(--ok);border-color:var(--ok);}
.progress-line {
    width:56px;
    height:3px;
    background:#1d2933;
    border-radius:2px;
    transition:var(--transition);
}
.progress-line.completed {background:var(--ok);box-shadow:0 0 0 1px rgba(43,207,136,0.25);}
.section-grid {display:grid;gap:3.2rem;}
.module {
    background:linear-gradient(180deg,#121a23,#10181f 80%);
    border:1px solid var(--border);
    border-radius:26px;
    padding:2.8rem 2.4rem 3rem;
    position:relative;
    overflow:hidden;
    transition:var(--transition);
}
.module:before {
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(135deg,rgba(4,210,210,0.09),transparent 65%);
    opacity:0;
    transition:var(--transition);
}
.module:hover:before {opacity:1;}
.module-header {
    display:flex;
    gap:1.8rem;
    flex-wrap:wrap;
    margin:0 0 2.1rem;
    align-items:flex-start;
}
.module-index {
    width:80px;
    height:80px;
    border-radius:20px;
    background:var(--accent-soft);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:1.65rem;
    color:var(--accent);
    border:1px solid var(--accent);
}
.module-info h3 {margin:0 0 .7rem;font-size:1.85rem;font-weight:700;letter-spacing:.6px;}
.module-info p {margin:0;font-size:.95rem;color:var(--text-dim);max-width:760px;}
.tag-row {display:flex;flex-wrap:wrap;gap:.55rem;margin:1.2rem 0 0;}
.tag-chip {
    font-size:.6rem;
    letter-spacing:1.3px;
    font-weight:600;
    text-transform:uppercase;
    padding:.45rem .7rem;
    border-radius:9px;
    border:1px solid var(--border);
    background:#141f26;
    color:var(--text-dim);
}
.box-cluster {display:grid;gap:1.6rem;margin-top:.6rem;}
.box {
    background:#15212b;
    border:1px solid var(--border);
    border-radius:18px;
    padding:1.6rem 1.5rem 1.9rem;
    transition:var(--transition);
    position:relative;
}
.box:hover {border-color:var(--accent);}
.box.accent {border-color:var(--accent);background:var(--accent-soft);}
.box.ok {border-color:var(--ok);background:var(--ok-soft);}
.box.danger {border-color:var(--danger);background:var(--danger-soft);}
.box.focus {border-color:var(--focus);background:var(--focus-soft);}
.box h4 {margin:0 0 1.1rem;font-size:1.02rem;font-weight:600;letter-spacing:.5px;color:var(--accent);}
.box.ok h4 {color:var(--ok);}
.box.danger h4 {color:var(--danger);}
.box.focus h4 {color:var(--focus);}
.box p {margin:0;font-size:.85rem;line-height:1.55;color:var(--text-dim);}
.box ul {margin:.4rem 0 0;padding:0;list-style:none;display:grid;gap:.55rem;}
.box ul li {position:relative;font-size:.78rem;line-height:1.45;padding-left:1.05rem;color:#c2d0db;}
.box ul li:before {
    content:'';
    position:absolute;
    left:0;
    top:.52rem;
    width:6px;
    height:6px;
    background:var(--accent);
    border-radius:50%;
    box-shadow:0 0 0 3px rgba(4,210,210,0.22);
}
.box.ok ul li:before {background:var(--ok);box-shadow:0 0 0 3px rgba(43,207,136,0.25);}
.box.danger ul li:before {background:var(--danger);box-shadow:0 0 0 3px rgba(255,77,82,0.25);}
.box.focus ul li:before {background:var(--focus);box-shadow:0 0 0 3px rgba(255,179,71,0.3);}
.scenario {
    margin-top:2.2rem;
    background:linear-gradient(180deg,#141f27,#132028);
    border:1px solid var(--border);
    border-radius:24px;
    padding:2.2rem 2rem 2.6rem;
    position:relative;
    overflow:hidden;
}
.scenario-title {margin:0 0 1.4rem;font-size:1.15rem;font-weight:600;letter-spacing:.5px;color:var(--accent);}
.phase-steps {display:flex;flex-wrap:wrap;gap:.6rem;margin:0 0 1.4rem;}
.phase-pill {
    font-size:.55rem;
    letter-spacing:1.3px;
    font-weight:600;
    text-transform:uppercase;
    padding:.5rem .75rem;
    border-radius:9px;
    background:#182933;
    color:var(--text-dim);
    border:1px solid #22313f;
    transition:var(--transition);
}
.phase-pill.active {background:var(--accent);color:#061115;border-color:var(--accent);}
.phase-pill.done {background:var(--ok);color:#041410;border-color:var(--ok);}
.term-wrap {
    display:grid;
    gap:1.3rem;
    grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
}
.terminal {
    background:#0c141b;
    border:1px solid #1d2b38;
    border-radius:18px;
    position:relative;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    box-shadow:0 4px 22px -6px rgba(0,0,0,.55);
}
.term-bar {
    display:flex;
    align-items:center;
    gap:.5rem;
    padding:.6rem .9rem;
    background:#101c25;
    border-bottom:1px solid #1d2b38;
    font-size:.62rem;
    letter-spacing:1.2px;
    text-transform:uppercase;
    font-weight:600;
    color:var(--text-dim);
}
.term-dots {display:flex;gap:.35rem;margin-right:.4rem;}
.term-dots span {
    width:11px;
    height:11px;
    border-radius:50%;
    background:#22313f;
}
.term-body {
    flex:1;
    padding:1.05rem 1rem 1.2rem;
    font-family:var(--mono);
    font-size:.66rem;
    line-height:1.35;
    color:#b9c6d2;
    overflow-y:auto;
    scrollbar-width:thin;
    scrollbar-color:#20303d transparent;
}
.term-body::-webkit-scrollbar {width:8px;}
.term-body::-webkit-scrollbar-track {background:transparent;}
.term-body::-webkit-scrollbar-thumb {background:#20303d;border-radius:4px;}
.term-line {opacity:0;transform:translateY(6px);transition:.4s ease;}
.term-line.show {opacity:1;transform:translateY(0);}
.term-cursor {
    display:inline-block;
    width:7px;
    height:12px;
    background:#04d2d2;
    margin-left:4px;
    animation:blink 1.2s steps(2,start) infinite;
    vertical-align:middle;
}
@keyframes blink {0%,49%{opacity:1}50%,100%{opacity:0}}
.term-actions {
    display:flex;
    flex-wrap:wrap;
    gap:.6rem;
    margin-top:1.1rem;
}
.term-btn {
    background:var(--accent-soft);
    border:1px solid var(--accent);
    color:var(--accent);
    font-size:.63rem;
    letter-spacing:1px;
    font-weight:600;
    text-transform:uppercase;
    padding:.65rem .95rem;
    border-radius:10px;
    cursor:pointer;
    transition:var(--transition);
    position:relative;
    overflow:hidden;
}
.term-btn:hover {background:var(--accent);color:#061115;box-shadow:var(--accent-glow);}
.term-btn.alt {background:var(--focus-soft);border-color:var(--focus);color:var(--focus);}
.term-btn.alt:hover {background:var(--focus);color:#14130b;}
.term-btn.danger {background:var(--danger-soft);border-color:var(--danger);color:var(--danger);}
.term-btn.danger:hover {background:var(--danger);color:#220709;}
.metric-grid {
    display:grid;
    gap:1rem;
    grid-template-columns:repeat(auto-fit,minmax(170px,1fr));
}
.metric-box {
    background:#101a22;
    border:1px solid #1e2a36;
    border-radius:15px;
    padding:1rem .95rem 1.15rem;
    display:flex;
    flex-direction:column;
    gap:.45rem;
    transition:var(--transition);
}
.metric-box:hover {border-color:var(--accent);}
.metric-label {
    font-size:.55rem;
    letter-spacing:1.3px;
    text-transform:uppercase;
    font-weight:600;
    color:var(--text-dim);
}
.metric-value {
    font-size:1.15rem;
    font-weight:600;
    background:linear-gradient(90deg,#04d2d2,#2bcf88);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}
.outcome-panel {
    background:#101a22;
    border:1px solid #1e2a36;
    border-radius:18px;
    padding:1.2rem 1.05rem 1.35rem;
    display:flex;
    flex-direction:column;
    gap:.85rem;
    position:relative;
}
.outcome-log {
    background:#0c141b;
    border:1px solid #1d2b38;
    border-radius:12px;
    padding:.85rem .9rem 1rem;
    font-family:var(--mono);
    font-size:.62rem;
    line-height:1.3;
    max-height:210px;
    overflow-y:auto;
    color:#b6c5d2;
}
.inline-disclaimer {
    margin-top:2.2rem;
    background:#121e25;
    border:1px solid #1d2b33;
    border-radius:18px;
    padding:1.6rem 1.5rem 1.7rem;
    font-size:.68rem;
    line-height:1.45;
    color:var(--text-dim);
}
.quiz-stage {
    margin:4rem 0 2.8rem;
    background:linear-gradient(180deg,#121a23,#10181f);
    border:1px solid var(--border);
    border-radius:28px;
    padding:3.2rem 2.4rem 3.4rem;
    position:relative;
    overflow:hidden;
}
.quiz-stage:before {
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(135deg,rgba(4,210,210,0.08),transparent 60%);
    pointer-events:none;
}
.quiz-header {text-align:center;margin:0 0 2.6rem;}
.quiz-title {margin:0 0 1rem;font-size:2.2rem;font-weight:700;letter-spacing:.6px;}
.quiz-desc {margin:0;font-size:.92rem;color:var(--text-dim);max-width:760px;margin-left:auto;margin-right:auto;}
.quiz-card {
    max-width:860px;
    margin:0 auto;
    background:#141f26;
    border:1px solid #1f2d39;
    border-radius:22px;
    padding:2rem 1.9rem 2.3rem;
    position:relative;
    transition:var(--transition);
}
.quiz-progress {
    display:flex;
    align-items:center;
    gap:.8rem;
    margin:0 0 1.4rem;
}
.quiz-bar {
    flex:1;
    height:10px;
    background:#1d2b33;
    border-radius:6px;
    overflow:hidden;
    position:relative;
}
.quiz-fill {
    height:100%;
    width:0;
    background:var(--grad-accent);
    border-radius:6px;
    transition:.5s cubic-bezier(.6,.05,.2,.95);
}
.quiz-step-indicator {
    font-size:.6rem;
    letter-spacing:1.3px;
    font-weight:600;
    color:var(--text-dim);
    text-transform:uppercase;
}
.q-body {min-height:160px;display:flex;flex-direction:column;gap:1.2rem;}
.q-text {font-size:1rem;line-height:1.45;font-weight:500;letter-spacing:.3px;color:#e5edf3;}
.q-options {display:grid;gap:.9rem;margin-top:.3rem;}
.q-option {
    background:#0f181f;
    border:1px solid #20313f;
    border-radius:14px;
    padding:.95rem 1.1rem 1.05rem;
    display:flex;
    gap:.75rem;
    align-items:flex-start;
    cursor:pointer;
    position:relative;
    transition:var(--transition);
}
.q-option:hover {border-color:var(--accent);background:var(--accent-soft);}
.q-option[data-selected="true"] {border-color:var(--focus);background:var(--focus-soft);}
.q-option.correct {border-color:var(--ok);background:var(--ok-soft);}
.q-option.incorrect {border-color:var(--danger);background:var(--danger-soft);}
.opt-letter {
    width:30px;
    height:30px;
    border-radius:10px;
    background:#18252f;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:600;
    font-size:.8rem;
    color:var(--text-dim);
    flex-shrink:0;
    transition:var(--transition);
}
.q-option[data-selected="true"] .opt-letter {background:var(--focus);color:#10140d;}
.q-option.correct .opt-letter {background:var(--ok);color:#061510;}
.q-option.incorrect .opt-letter {background:var(--danger);color:#1c0707;}
.opt-text {font-size:.79rem;line-height:1.4;color:#c0cdd8;font-weight:500;flex:1;}
.q-explanation {
    display:none;
    margin-top:1.1rem;
    background:#101d23;
    border:1px solid #20313f;
    padding:1rem 1rem 1.15rem;
    border-radius:14px;
    font-size:.7rem;
    line-height:1.45;
    color:#a9bac7;
}
.q-explanation.show {display:block;animation:fadeIn .4s ease;}
.quiz-actions {
    display:flex;
    gap:.85rem;
    flex-wrap:wrap;
    margin-top:2rem;
    justify-content:center;
}
.quiz-btn {
    background:var(--accent-soft);
    border:1px solid var(--accent);
    color:var(--accent);
    border-radius:14px;
    padding:.85rem 1.5rem .95rem;
    font-size:.72rem;
    letter-spacing:1px;
    font-weight:600;
    text-transform:uppercase;
    cursor:pointer;
    transition:var(--transition);
    position:relative;
    overflow:hidden;
}
.quiz-btn:hover {background:var(--accent);color:#041315;box-shadow:var(--accent-glow);}
.quiz-btn.primary {background:var(--grad-accent);border:none;color:#061013;}
.quiz-btn.primary:hover {transform:translateY(-3px);box-shadow:0 10px 28px -6px rgba(4,210,210,0.5);}
.quiz-btn.danger {background:var(--danger-soft);border:1px solid var(--danger);color:var(--danger);}
.quiz-btn.danger:hover {background:var(--danger);color:#1a0606;}
.quiz-results {
    margin-top:2.6rem;
    text-align:center;
    display:none;
    animation:fadeIn .5s ease;
}
.quiz-results.show {display:block;}
.result-score {
    font-size:3.2rem;
    font-weight:700;
    background:linear-gradient(90deg,#04d2d2,#2bcf88);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    margin:0 0 1.1rem;
}
.result-message {font-size:.9rem;max-width:640px;margin:0 auto 1.8rem;color:var(--text-dim);}
.final-complete {display:none;}
.navigation {
    background:#121d25;
    border:1px solid var(--border);
    border-radius:18px;
    padding:2rem 1.8rem;
    text-align:center;
    margin:3.8rem 0 2.4rem;
}
.nav-btn {
    display:inline-flex;
    gap:.55rem;
    align-items:center;
    background:var(--accent-soft);
    border:1px solid var(--accent);
    color:var(--accent);
    padding:.85rem 1.4rem .95rem;
    border-radius:14px;
    font-size:.7rem;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:1px;
    text-decoration:none;
    transition:var(--transition);
}
.nav-btn:hover {background:var(--accent);color:#071013;box-shadow:var(--accent-glow);text-decoration:none;}
@keyframes fadeIn {from {opacity:0;transform:translateY(12px)} to {opacity:1;transform:translateY(0)}}
@media (max-width:960px){
    .module {padding:2.4rem 1.6rem 2.6rem;}
    .module-info h3 {font-size:1.55rem;}
    .module-index {width:66px;height:66px;font-size:1.35rem;}
    .tutorial-hero {padding:3.9rem 0 3rem;}
    .progress-step {width:46px;height:46px;}
}
@media (max-width:640px){
    .hero-title {font-size:2.2rem;}
    .quiz-card {padding:1.7rem 1.3rem 1.9rem;}
    .q-option {padding:.75rem .85rem .85rem;}
    .module {padding:2.1rem 1.3rem 2.2rem;}
    .module-index {width:58px;height:58px;font-size:1.2rem;}
}
</style>

<div class="tutorial-hero">
    <div class="hero-inner container">
        <div class="hero-badge">Advanced Cloud Security Exploits</div>
        <h1 class="hero-title">Adversarial Cloud Exploit Pathway Simulation</h1>
        <p class="hero-sub">Deep scenario modeling of exploit trajectories across identity federation pivoting, metadata exposure resilience, container breakout drift, artifact integrity divergence, covert persistence channels, event trigger chaining, lateral identity movement, and staged exfil resilience. All interactions are abstracted, defensively framed, and safe for instructional analysis.</p>
        <div class="metrics">
            <div class="metric">
                <div class="metric-value">8</div>
                <div class="metric-label">Exploit Paths</div>
            </div>
            <div class="metric">
                <div class="metric-value">12</div>
                <div class="metric-label">Assessment Items</div>
            </div>
            <div class="metric">
                <div class="metric-value">180</div>
                <div class="metric-label">XP Value</div>
            </div>
            <div class="metric">
                <div class="metric-value">Expert</div>
                <div class="metric-label">Tier</div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php if($tutorial_completed): ?>
    <div class="completion-status">
        <div class="icon">✔️</div>
        <div>You have completed this module. You may review simulations and retake the assessment without further XP.</div>
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

    <div class="section-grid">
        <div class="module" data-step="1">
            <div class="module-header">
                <div class="module-index">1</div>
                <div class="module-info">
                    <h3>Identity Federation Role Pivot Chain</h3>
                    <p>Abstracted modeling of privilege escalation via externally trusted role pivot sequences and progressive privilege layering detection.</p>
                    <div class="tag-row">
                        <div class="tag-chip">Identity</div>
                        <div class="tag-chip">Privilege Drift</div>
                        <div class="tag-chip">Cross Account</div>
                        <div class="tag-chip">Anomaly Fusion</div>
                    </div>
                </div>
            </div>
            <div class="box-cluster">
                <div class="box accent">
                    <h4>Scenario Summary</h4>
                    <p>External principal obtains unintended assume capability. Escalation chain shortens intervals between increasingly privileged roles, enabling detection through sequence compression and privilege drift scoring.</p>
                </div>
                <div class="box focus">
                    <h4>Key Signals</h4>
                    <ul>
                        <li>Role assume temporal compression</li>
                        <li>Privilege delta amplification</li>
                        <li>Anomalous external principal lineage</li>
                        <li>Burst enumeration following first assume</li>
                    </ul>
                </div>
                <div class="box ok">
                    <h4>Mitigation Priorities</h4>
                    <ul>
                        <li>Conditional external identity gating</li>
                        <li>Session invalidation cadence enforcement</li>
                        <li>Trust boundary minimization</li>
                        <li>Privilege path length monitoring</li>
                    </ul>
                </div>
            </div>
            <div class="scenario" data-scenario="identity-pivot">
                <h4 class="scenario-title">Interactive Chain Simulation</h4>
                <div class="phase-steps" data-phase-container></div>
                <div class="term-wrap">
                    <div class="terminal" data-terminal>
                        <div class="term-bar">
                            <div class="term-dots"><span></span><span></span><span></span></div>
                            SESSION TRACE
                        </div>
                        <div class="term-body" data-term-body></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="stream">Stream</button>
                            <button class="term-btn alt" data-action="next">Next Phase</button>
                            <button class="term-btn" data-action="score">Score</button>
                            <button class="term-btn danger" data-action="reset">Reset</button>
                        </div>
                    </div>
                    <div class="outcome-panel">
                        <div class="metric-grid" data-metrics>
                            <div class="metric-box"><div class="metric-label">Distinct Principals</div><div class="metric-value" data-m="principals">0</div></div>
                            <div class="metric-box"><div class="metric-label">Burst Score</div><div class="metric-value" data-m="burst">0</div></div>
                            <div class="metric-box"><div class="metric-label">Drift Percent</div><div class="metric-value" data-m="drift">0%</div></div>
                            <div class="metric-box"><div class="metric-label">Containment</div><div class="metric-value" data-m="contain">0%</div></div>
                        </div>
                        <div class="outcome-log" data-outcome></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="analyze">Analyze State</button>
                        </div>
                    </div>
                </div>
                <div class="inline-disclaimer">Abstracted behavioral model. No real credentials or actionable exploitation steps involved. Focus on detection sequence interpretation.</div>
            </div>
        </div>

        <div class="module" data-step="2">
            <div class="module-header">
                <div class="module-index">2</div>
                <div class="module-info">
                    <h3>Metadata Credential Exposure Attempt</h3>
                    <p>Resilience modeling against abstracted SSRF-based metadata credential access patterns and adaptive egress control response.</p>
                    <div class="tag-row">
                        <div class="tag-chip">Metadata</div>
                        <div class="tag-chip">Egress Control</div>
                        <div class="tag-chip">Session Tokens</div>
                        <div class="tag-chip">Anomaly</div>
                    </div>
                </div>
            </div>
            <div class="box-cluster">
                <div class="box accent">
                    <h4>Scenario Summary</h4>
                    <p>Unusual internal path request triggers anomaly. Temporary token acquired. Egress and anomaly fusion detection reduce exposure time.</p>
                </div>
                <div class="box focus">
                    <h4>Detection Anchors</h4>
                    <ul>
                        <li>Metadata path variance spike</li>
                        <li>Short-lived credential reassignment misuse</li>
                        <li>Outbound domain mismatch</li>
                        <li>Fusion alert correlation</li>
                    </ul>
                </div>
                <div class="box ok">
                    <h4>Mitigation Scope</h4>
                    <ul>
                        <li>Hardened metadata session tokens</li>
                        <li>Egress domain allow-list</li>
                        <li>Adaptive anomaly gating</li>
                        <li>Credential lifetime reduction</li>
                    </ul>
                </div>
            </div>
            <div class="scenario" data-scenario="metadata-ssrf">
                <h4 class="scenario-title">Interactive Exposure Simulation</h4>
                <div class="phase-steps" data-phase-container></div>
                <div class="term-wrap">
                    <div class="terminal" data-terminal>
                        <div class="term-bar">
                            <div class="term-dots"><span></span><span></span><span></span></div>
                            METADATA FLOW
                        </div>
                        <div class="term-body" data-term-body></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="stream">Stream</button>
                            <button class="term-btn alt" data-action="next">Next Phase</button>
                            <button class="term-btn" data-action="score">Score</button>
                            <button class="term-btn danger" data-action="reset">Reset</button>
                        </div>
                    </div>
                    <div class="outcome-panel">
                        <div class="metric-grid">
                            <div class="metric-box"><div class="metric-label">Probes</div><div class="metric-value" data-m="probes">0</div></div>
                            <div class="metric-box"><div class="metric-label">Tokens</div><div class="metric-value" data-m="tokens">0</div></div>
                            <div class="metric-box"><div class="metric-label">Anomalies</div><div class="metric-value" data-m="anomalies">0</div></div>
                            <div class="metric-box"><div class="metric-label">Mitigation</div><div class="metric-value" data-m="mitigation">0%</div></div>
                        </div>
                        <div class="outcome-log" data-outcome></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="analyze">Analyze State</button>
                        </div>
                    </div>
                </div>
                <div class="inline-disclaimer">Abstracted network and identity events only. No real network exploitation enacted.</div>
            </div>
        </div>

        <div class="module" data-step="3">
            <div class="module-header">
                <div class="module-index">3</div>
                <div class="module-info">
                    <h3>Container Breakout Drift Sequence</h3>
                    <p>Runtime drift scoring simulation emphasizing sensitive path access monitoring and containment response effectiveness.</p>
                    <div class="tag-row">
                        <div class="tag-chip">Workload</div>
                        <div class="tag-chip">Runtime</div>
                        <div class="tag-chip">Drift</div>
                        <div class="tag-chip">Node Boundary</div>
                    </div>
                </div>
            </div>
            <div class="box-cluster">
                <div class="box accent">
                    <h4>Scenario Summary</h4>
                    <p>Container process attempts host traversal and credential enumeration. Drift spike triggers adaptive policy tightening.</p>
                </div>
                <div class="box focus">
                    <h4>Key Metrics</h4>
                    <ul>
                        <li>Syscall variance</li>
                        <li>Namespace divergence</li>
                        <li>Sensitive path access attempts</li>
                        <li>Residual drift after constraints</li>
                    </ul>
                </div>
                <div class="box ok">
                    <h4>Containment Steps</h4>
                    <ul>
                        <li>Read-only root enforcement</li>
                        <li>Seccomp profile tightening</li>
                        <li>Limited credential surface</li>
                        <li>Continuous drift recalibration</li>
                    </ul>
                </div>
            </div>
            <div class="scenario" data-scenario="container-breakout">
                <h4 class="scenario-title">Runtime Drift Simulation</h4>
                <div class="phase-steps" data-phase-container></div>
                <div class="term-wrap">
                    <div class="terminal" data-terminal>
                        <div class="term-bar">
                            <div class="term-dots"><span></span><span></span><span></span></div>
                            RUNTIME TRACE
                        </div>
                        <div class="term-body" data-term-body></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="stream">Stream</button>
                            <button class="term-btn alt" data-action="next">Next Phase</button>
                            <button class="term-btn" data-action="score">Score</button>
                            <button class="term-btn danger" data-action="reset">Reset</button>
                        </div>
                    </div>
                    <div class="outcome-panel">
                        <div class="metric-grid">
                            <div class="metric-box"><div class="metric-label">Syscall Var</div><div class="metric-value" data-m="sys">0%</div></div>
                            <div class="metric-box"><div class="metric-label">Path Attempts</div><div class="metric-value" data-m="paths">0</div></div>
                            <div class="metric-box"><div class="metric-label">Drift Score</div><div class="metric-value" data-m="drift">0</div></div>
                            <div class="metric-box"><div class="metric-label">Containment</div><div class="metric-value" data-m="contain">0%</div></div>
                        </div>
                        <div class="outcome-log" data-outcome></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="analyze">Analyze State</button>
                        </div>
                    </div>
                </div>
                <div class="inline-disclaimer">No privileged instructions. Behavioral abstraction for defensive reasoning only.</div>
            </div>
        </div>

        <div class="module" data-step="4">
            <div class="module-header">
                <div class="module-index">4</div>
                <div class="module-info">
                    <h3>Artifact Supply Chain Integrity Divergence</h3>
                    <p>Verification pipeline modeling for early detection of tampered artifacts via signature mismatch and dependency graph drift.</p>
                    <div class="tag-row">
                        <div class="tag-chip">Supply Chain</div>
                        <div class="tag-chip">Integrity</div>
                        <div class="tag-chip">Provenance</div>
                        <div class="tag-chip">Quarantine</div>
                    </div>
                </div>
            </div>
            <div class="box-cluster">
                <div class="box accent">
                    <h4>Scenario Summary</h4>
                    <p>Artifact introduced with altered dependency subtree and mismatched provenance signature, quarantined pre-promotion.</p>
                </div>
                <div class="box focus">
                    <h4>Detection Layers</h4>
                    <ul>
                        <li>Signature mismatch detection</li>
                        <li>Graph structural deviation</li>
                        <li>Unregistered build source fingerprint</li>
                        <li>Quarantine isolation depth</li>
                    </ul>
                </div>
                <div class="box ok">
                    <h4>Protective Controls</h4>
                    <ul>
                        <li>Immutable build lineage</li>
                        <li>Policy driven signing gates</li>
                        <li>Digest watch lists</li>
                        <li>Attestation continuity</li>
                    </ul>
                </div>
            </div>
            <div class="scenario" data-scenario="artifact-supply">
                <h4 class="scenario-title">Integrity Pipeline Simulation</h4>
                <div class="phase-steps" data-phase-container></div>
                <div class="term-wrap">
                    <div class="terminal" data-terminal>
                        <div class="term-bar">
                            <div class="term-dots"><span></span><span></span><span></span></div>
                            INTEGRITY AUDIT
                        </div>
                        <div class="term-body" data-term-body></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="stream">Stream</button>
                            <button class="term-btn alt" data-action="next">Next Phase</button>
                            <button class="term-btn" data-action="score">Score</button>
                            <button class="term-btn danger" data-action="reset">Reset</button>
                        </div>
                    </div>
                    <div class="outcome-panel">
                        <div class="metric-grid">
                            <div class="metric-box"><div class="metric-label">Signatures</div><div class="metric-value" data-m="sig">0</div></div>
                            <div class="metric-box"><div class="metric-label">Graph Delta</div><div class="metric-value" data-m="delta">0%</div></div>
                            <div class="metric-box"><div class="metric-label">Quarantine</div><div class="metric-value" data-m="q">0</div></div>
                            <div class="metric-box"><div class="metric-label">Recovery</div><div class="metric-value" data-m="rec">0%</div></div>
                        </div>
                        <div class="outcome-log" data-outcome></div>
                        <div class="term-actions">
                            <button class="term-btn" data-action="analyze">Analyze State</button>
                        </div>
                    </div>
                </div>
                <div class="inline-disclaimer">Illustrative supply chain intrusion detection logic. No actionable exploit techniques.</div>
            </div>
        </div>

        <div class="module" data-step="5">
            <div class="module-header">
                <div class="module-index">5</div>
                <div class="module-info">
                    <h3>Covert Persistence Channel Modeling</h3>
                    <p>Simulated covert channel entropy and mutation drift scoring across metadata tag surfaces.</p>
                    <div class="tag-row">
                        <div class="tag-chip">Persistence</div>
                        <div class="tag-chip">Entropy</div>
                        <div class="tag-chip">Metadata</div>
                        <div class="tag-chip">Anomaly</div>
                    </div>
                </div>
            </div>
            <div class="box-cluster">
                <div class="box focus">
                    <h4>Detection Metrics</h4>
                    <ul>
                        <li>Entropy divergence index</li>
                        <li>Mutation rate acceleration</li>
                        <li>Character distribution shift</li>
                        <li>Quarantine flag threshold</li>
                    </ul>
                </div>
                <div class="box ok">
                    <h4>Mitigation Levers</h4>
                    <ul>
                        <li>Restricted mutation roles</li>
                        <li>Adaptive quarantine enforcement</li>
                        <li>Normalization sweeps</li>
                        <li>Real-time deviation scoring</li>
                    </ul>
                </div>
            </div>
            <div class="inline-disclaimer">Entropy modeling only. No covert data transfer implemented.</div>
        </div>

        <div class="module" data-step="6">
            <div class="module-header">
                <div class="module-index">6</div>
                <div class="module-info">
                    <h3>Event Trigger Privilege Chain</h3>
                    <p>Abstracted function invocation escalation through permissive event subscription hierarchy modeling.</p>
                    <div class="tag-row">
                        <div class="tag-chip">Events</div>
                        <div class="tag-chip">Least Privilege</div>
                        <div class="tag-chip">Chaining</div>
                        <div class="tag-chip">Restriction</div>
                    </div>
                </div>
            </div>
            <div class="inline-disclaimer">Event chain simulation highlights detection of invocations lacking provenance alignment.</div>
        </div>

        <div class="module" data-step="7">
            <div class="module-header">
                <div class="module-index">7</div>
                <div class="module-info">
                    <h3>Lateral Identity Movement Modeling</h3>
                    <p>Identity impersonation chain complexity scoring and privilege overlap risk interpretation.</p>
                </div>
            </div>
            <div class="inline-disclaimer">No real impersonation performed. Chain metrics illustrate early containment decision points.</div>
        </div>

        <div class="module" data-step="8">
            <div class="module-header">
                <div class="module-index">8</div>
                <div class="module-info">
                    <h3>Staged Exfiltration Resilience</h3>
                    <p>Detection of abstracted exfil staging patterns through composite signal fusion on short-lived access constructs.</p>
                </div>
            </div>
            <div class="inline-disclaimer">Abstracted staging evolution only. Emphasizes layered signal correlation for defensive action timing.</div>
        </div>
    </div>

    <div class="quiz-stage" id="assessment">
        <div class="quiz-header">
            <h2 class="quiz-title">Expert Pathway Assessment</h2>
            <p class="quiz-desc">Single-question progressive interface. Each answer reveals rationale. Complete all items to obtain final resilience interpretation score.</p>
        </div>
        <div class="quiz-card" id="quizCard">
            <div class="quiz-progress">
                <div class="quiz-step-indicator" id="quizIndicator">Question 1 / 12</div>
                <div class="quiz-bar"><div class="quiz-fill" id="quizFill"></div></div>
            </div>
            <div class="q-body" id="qBody"></div>
            <div class="q-explanation" id="qExplanation">
                <div class="q-exp-text" id="qExpText"></div>
            </div>
            <div class="quiz-actions">
                <button class="quiz-btn primary" id="submitAnswer" disabled>Submit</button>
                <button class="quiz-btn" id="nextQuestion" style="display:none;">Next</button>
                <button class="quiz-btn danger" id="restartQuiz" style="display:none;">Restart</button>
            </div>
        </div>
        <div class="quiz-results" id="quizResults">
            <div class="result-score" id="finalScore">0%</div>
            <div class="result-message" id="finalMessage"></div>
            <?php if(!$tutorial_completed): ?>
            <button class="quiz-btn primary final-complete" id="completeTutorial">Complete Module (+180 XP)</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="navigation">
        <a href="https://learnit.systems/tutorials/index.php" class="nav-btn">Back To Tutorials</a>
    </div>
</div>

<script>
const scenarioData=<?php echo json_encode($scenarios); ?>;
function initScenario(sc){
    const el=document.querySelector('.scenario[data-scenario="'+sc.id+'"]');
    if(!el) return;
    const phases=el.querySelector('[data-phase-container]');
    sc.phases.forEach((p,i)=>{
        const pill=document.createElement('div');
        pill.className='phase-pill'+(i===0?' active':'');
        pill.textContent=p;
        phases.appendChild(pill);
    });
    const termBody=el.querySelector('[data-term-body]');
    const outcome=el.querySelector('[data-outcome]');
    const metricsMap={};
    el.querySelectorAll('[data-m]').forEach(m=>metricsMap[m.getAttribute('data-m')]=m);
    let pointer=0;
    let phaseIndex=0;
    let streaming=false;
    function renderLine(text){
        const line=document.createElement('div');
        line.className='term-line';
        line.textContent=text;
        termBody.appendChild(line);
        requestAnimationFrame(()=>line.classList.add('show'));
        termBody.scrollTop=termBody.scrollHeight;
    }
    function advanceLine(){
        if(pointer>=sc.terminal.length){
            streaming=false;
            return;
        }
        renderLine(sc.terminal[pointer]);
        pointer++;
        if(streaming) setTimeout(advanceLine,320);
    }
    function markPhase(){
        const pills=phases.querySelectorAll('.phase-pill');
        pills.forEach((p,i)=>{
            p.classList.remove('active');
            if(i<phaseIndex) p.classList.add('done');
        });
        if(pills[phaseIndex]) pills[phaseIndex].classList.add('active');
    }
    function updateMetrics(){
        if(sc.id==='identity-pivot'){
            if(pointer>1) metricsMap.principals.textContent='2';
            if(pointer>3) metricsMap.burst.textContent='9';
            if(pointer>4) metricsMap.drift.textContent='17%';
            if(pointer>6) metricsMap.contain.textContent='85%';
        }
        if(sc.id==='metadata-ssrf'){
            if(pointer>1) metricsMap.probes.textContent='1';
            if(pointer>3) metricsMap.tokens.textContent='1';
            if(pointer>5) metricsMap.anomalies.textContent='1';
            if(pointer>7) metricsMap.mitigation.textContent='90%';
        }
        if(sc.id==='container-breakout'){
            if(pointer>1) metricsMap.sys.textContent='12%';
            if(pointer>3) metricsMap.paths.textContent='1';
            if(pointer>4) metricsMap.drift.textContent='34';
            if(pointer>6) metricsMap.contain.textContent='78%';
        }
        if(sc.id==='artifact-supply'){
            if(pointer>2) metricsMap.sig.textContent='1';
            if(pointer>4) metricsMap.delta.textContent='23%';
            if(pointer>6) metricsMap.q.textContent='1';
            if(pointer>7) metricsMap.rec.textContent='42%';
        }
    }
    el.querySelectorAll('.term-btn').forEach(btn=>{
        btn.addEventListener('click',()=>{
            const action=btn.getAttribute('data-action');
            if(action==='stream'){
                if(streaming)return;
                streaming=true;
                advanceLineLoop();
            }
            if(action==='next'){
                if(phaseIndex<sc.phases.length-1){
                    phaseIndex++;
                    markPhase();
                }
            }
            if(action==='reset'){
                streaming=false;
                pointer=0;
                phaseIndex=0;
                termBody.innerHTML='';
                outcome.innerHTML='';
                el.querySelectorAll('.phase-pill').forEach((x,i)=>{
                    x.className='phase-pill'+(i===0?' active':'');
                });
                Object.values(metricsMap).forEach(m=>{
                    if(m.textContent.endsWith('%')) m.textContent='0%'; else m.textContent='0';
                });
            }
            if(action==='score'){
                const score=Math.min(100,Math.round((pointer/sc.terminal.length)*100));
                outcome.textContent+='Resilience Score '+score+'%\n';
                outcome.scrollTop=outcome.scrollHeight;
            }
            if(action==='analyze'){
                outcome.textContent+='Analysis pivot='+phaseIndex+' lines='+pointer+'\n';
                outcome.scrollTop=outcome.scrollHeight;
            }
        });
    });
    function advanceLineLoop(){
        if(!streaming)return;
        if(pointer<sc.terminal.length){
            renderLine(sc.terminal[pointer]);
            pointer++;
            updateMetrics();
            setTimeout(advanceLineLoop,380);
        } else {
            streaming=false;
        }
    }
}
scenarioData.forEach(initScenario);

const quizData=<?php echo json_encode($randomized_questions); ?>;
let quizIndex=0;
let quizScore=0;
let quizAnswered=false;
const qBody=document.getElementById('qBody');
const qExplanation=document.getElementById('qExplanation');
const qExpText=document.getElementById('qExpText');
const quizIndicator=document.getElementById('quizIndicator');
const quizFill=document.getElementById('quizFill');
const submitAnswer=document.getElementById('submitAnswer');
const nextQuestion=document.getElementById('nextQuestion');
const restartQuiz=document.getElementById('restartQuiz');
const quizResults=document.getElementById('quizResults');
const finalScore=document.getElementById('finalScore');
const finalMessage=document.getElementById('finalMessage');
const completeBtn=document.getElementById('completeTutorial');

function renderQuestion(){
    quizAnswered=false;
    submitAnswer.disabled=true;
    qExplanation.classList.remove('show');
    qBody.innerHTML='';
    const q=quizData[quizIndex];
    quizIndicator.textContent='Question '+(quizIndex+1)+' / '+quizData.length;
    quizFill.style.width=((quizIndex)/quizData.length*100)+'%';
    const qt=document.createElement('div');
    qt.className='q-text';
    qt.textContent=q.question;
    qBody.appendChild(qt);
    const opts=document.createElement('div');
    opts.className='q-options';
    q.options.forEach((op,i)=>{
        const opt=document.createElement('div');
        opt.className='q-option';
        opt.setAttribute('data-index',i);
        opt.innerHTML='<div class="opt-letter">'+String.fromCharCode(65+i)+'</div><div class="opt-text">'+op+'</div>';
        opt.addEventListener('click',()=>{
            if(quizAnswered)return;
            opts.querySelectorAll('.q-option').forEach(o=>o.setAttribute('data-selected','false'));
            opt.setAttribute('data-selected','true');
            submitAnswer.disabled=false;
        });
        opts.appendChild(opt);
    });
    qBody.appendChild(opts);
}
renderQuestion();

submitAnswer.addEventListener('click',()=>{
    if(quizAnswered)return;
    quizAnswered=true;
    const q=quizData[quizIndex];
    const selected=document.querySelector('.q-option[data-selected="true"]');
    const chosen=selected?parseInt(selected.getAttribute('data-index')):-1;
    const options=document.querySelectorAll('.q-option');
    options.forEach(o=>{
        const idx=parseInt(o.getAttribute('data-index'));
        if(idx===q.correct){
            o.classList.add('correct');
        } else if(idx===chosen){
            o.classList.add('incorrect');
        }
        o.style.pointerEvents='none';
    });
    if(chosen===q.correct) quizScore++;
    qExpText.textContent=q.explanation;
    qExplanation.classList.add('show');
    submitAnswer.style.display='none';
    nextQuestion.style.display='inline-block';
    if(quizIndex===quizData.length-1){
        nextQuestion.textContent='Finish';
    }
});

nextQuestion.addEventListener('click',()=>{
    if(quizIndex<quizData.length-1){
        quizIndex++;
        submitAnswer.style.display='inline-block';
        nextQuestion.style.display='none';
        submitAnswer.disabled=true;
        renderQuestion();
    } else {
        finalizeQuiz();
    }
});

restartQuiz.addEventListener('click',()=>{
    quizIndex=0;
    quizScore=0;
    quizResults.classList.remove('show');
    document.getElementById('quizCard').style.display='block';
    submitAnswer.style.display='inline-block';
    nextQuestion.style.display='none';
    restartQuiz.style.display='none';
    renderQuestion();
});

function finalizeQuiz(){
    const pct=Math.round((quizScore/quizData.length)*100);
    document.getElementById('quizCard').style.display='none';
    quizResults.classList.add('show');
    finalScore.textContent=pct+'%';
    let msg='';
    if(pct>=85){
        msg='Outstanding detection mapping and mitigation prioritization alignment.';
        if(completeBtn) completeBtn.style.display='inline-block';
    } else if(pct>=70){
        msg='Strong comprehension. Deepen chain correlation insights to reach optimal mastery.';
    } else {
        msg='Foundational understanding present. Re-engage scenario terminals to strengthen layered interpretation.';
    }
    finalMessage.textContent=msg;
    restartQuiz.style.display='inline-block';
    quizFill.style.width='100%';
}

if(completeBtn){
    completeBtn.addEventListener('click',()=>{
        completeBtn.disabled=true;
        completeBtn.textContent='Completing...';
        fetch('../api/complete-tutorial.php',{
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                tutorial_id:'<?php echo $tutorial_id; ?>',
                user_id:<?php echo $user_id; ?>,
                username:'<?php echo htmlspecialchars($username); ?>',
                points:180,
                timestamp:'<?php echo $timestamp; ?>'
            })
        }).then(r=>r.json())
        .then(d=>{
            if(d.success){
                completeBtn.textContent='Module Completed (+180 XP)';
                completeBtn.style.background='linear-gradient(135deg,#2bcf88,#04d2d2)';
                setTimeout(()=>{window.location.href='../tutorials/';},1600);
            } else {
                completeBtn.disabled=false;
                completeBtn.textContent='Complete Module (+180 XP)';
                alert('Error completing module. Try again.');
            }
        }).catch(()=>{
            completeBtn.disabled=false;
            completeBtn.textContent='Complete Module (+180 XP)';
            alert('Error completing module. Try again.');
        });
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
$role       = $_SESSION['role'] ?? 'student';
$aiEndpoint = BASE_URL . "?url=$role/ai-chat";
?>
<div class="ai-chat-container">
  <div class="ai-chat-header">
    <div class="ai-header-left">
      <div class="ai-avatar-big">✦</div>
      <div>
        <h3>UniVerse AI Assistant</h3>
        <p>Powered by Claude · Ask me anything about academics</p>
      </div>
    </div>
    <button class="btn btn-secondary btn-sm" onclick="clearChat()">Clear Chat</button>
  </div>

  <div class="ai-messages" id="aiMessages">
    <div class="ai-msg ai-msg-bot">
      <div class="ai-msg-avatar">✦</div>
      <div class="ai-msg-bubble">
        <p>Hello! I'm <strong>UniVerse AI</strong>, your intelligent university assistant.</p>
        <p>I can help you with:</p>
        <ul>
          <?php if ($role === 'admin'): ?>
          <li>📊 Analyzing student performance trends</li>
          <li>📋 Academic planning and scheduling</li>
          <li>🏫 University administration guidance</li>
          <li>💡 Policy recommendations</li>
          <?php elseif ($role === 'faculty'): ?>
          <li>📝 Creating lesson plans and rubrics</li>
          <li>📊 Analyzing student performance</li>
          <li>🧪 Assignment and exam design</li>
          <li>📚 Research guidance</li>
          <?php else: ?>
          <li>📚 Study strategies and tips</li>
          <li>📊 Understanding your grades and CGPA</li>
          <li>🎯 Career guidance and planning</li>
          <li>💡 Concept explanations</li>
          <?php endif; ?>
        </ul>
        <p>What can I help you with today?</p>
      </div>
    </div>
  </div>

  <div class="ai-input-area">
    <div class="ai-suggestions">
      <?php if ($role === 'student'): ?>
      <button class="ai-chip" onclick="sendSuggestion(this)">How can I improve my CGPA?</button>
      <button class="ai-chip" onclick="sendSuggestion(this)">Tips for exam preparation</button>
      <button class="ai-chip" onclick="sendSuggestion(this)">Best CS career paths in AI?</button>
      <?php elseif ($role === 'faculty'): ?>
      <button class="ai-chip" onclick="sendSuggestion(this)">Create a grading rubric for AI assignments</button>
      <button class="ai-chip" onclick="sendSuggestion(this)">Best practices for online teaching</button>
      <button class="ai-chip" onclick="sendSuggestion(this)">How to identify struggling students early?</button>
      <?php else: ?>
      <button class="ai-chip" onclick="sendSuggestion(this)">Analyze enrollment trends</button>
      <button class="ai-chip" onclick="sendSuggestion(this)">Strategies to improve attendance</button>
      <button class="ai-chip" onclick="sendSuggestion(this)">How to boost student CGPA university-wide?</button>
      <?php endif; ?>
    </div>
    <div class="ai-input-row">
      <textarea id="aiInput" placeholder="Ask anything…" rows="1" onkeydown="aiKeydown(event)" oninput="autoResize(this)"></textarea>
      <button class="ai-send-btn" id="aiSendBtn" onclick="sendMessage()">
        <span>▶</span>
      </button>
    </div>
  </div>
</div>

<script>
const AI_ENDPOINT = '<?= $aiEndpoint ?>';
let chatHistory = [];

function sendSuggestion(btn) {
  document.getElementById('aiInput').value = btn.textContent;
  sendMessage();
}

function aiKeydown(e) {
  if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
}

function autoResize(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 140) + 'px';
}

async function sendMessage() {
  const input = document.getElementById('aiInput');
  const msg   = input.value.trim();
  if (!msg) return;

  appendMessage(msg, 'user');
  input.value = '';
  input.style.height = 'auto';

  const btn = document.getElementById('aiSendBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="ai-spinner"></span>';

  appendThinking();

  try {
    const res  = await fetch(AI_ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `message=${encodeURIComponent(msg)}`,
    });
    const data = await res.json();
    removeThinking();
    appendMessage(data.response || 'Sorry, I could not process that.', 'bot');
  } catch (e) {
    removeThinking();
    appendMessage('Network error. Please try again.', 'bot');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<span>▶</span>';
  }
}

function appendMessage(text, type) {
  const msgs = document.getElementById('aiMessages');
  const div  = document.createElement('div');
  div.className = `ai-msg ai-msg-${type}`;
  const parsed = type === 'bot' ? markdownToHtml(text) : escapeHtml(text);
  div.innerHTML = type === 'bot'
    ? `<div class="ai-msg-avatar">✦</div><div class="ai-msg-bubble">${parsed}</div>`
    : `<div class="ai-msg-bubble">${parsed}</div>`;
  msgs.appendChild(div);
  msgs.scrollTop = msgs.scrollHeight;

  // hide suggestion chips after first user message
  if (type === 'user') document.querySelector('.ai-suggestions').style.display = 'none';
}

function appendThinking() {
  const msgs = document.getElementById('aiMessages');
  const div  = document.createElement('div');
  div.id = 'aiThinking';
  div.className = 'ai-msg ai-msg-bot';
  div.innerHTML = `<div class="ai-msg-avatar">✦</div><div class="ai-msg-bubble ai-thinking"><span></span><span></span><span></span></div>`;
  msgs.appendChild(div);
  msgs.scrollTop = msgs.scrollHeight;
}

function removeThinking() {
  document.getElementById('aiThinking')?.remove();
}

function clearChat() {
  const msgs = document.getElementById('aiMessages');
  while (msgs.children.length > 1) msgs.removeChild(msgs.lastChild);
  document.querySelector('.ai-suggestions').style.display = 'flex';
}

function escapeHtml(t) {
  return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
}

function markdownToHtml(text) {
  return text
    .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
    .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
    .replace(/\*(.+?)\*/g,'<em>$1</em>')
    .replace(/`(.+?)`/g,'<code>$1</code>')
    .replace(/^### (.+)$/gm,'<h4>$1</h4>')
    .replace(/^## (.+)$/gm,'<h3>$1</h3>')
    .replace(/^# (.+)$/gm,'<h2>$1</h2>')
    .replace(/^\- (.+)$/gm,'<li>$1</li>')
    .replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>')
    .replace(/\n\n/g,'</p><p>')
    .replace(/\n/g,'<br>');
}
</script>

<!-- AI Chatbot Widget -->
<style>
    #ai-chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }
    #chatbot-bubble {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #6e42c1 0%, #4e73df 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    #chatbot-bubble:hover {
        transform: scale(1.1) rotate(5deg);
    }
    #chatbot-window {
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
        animation: slideUp 0.4s ease-out;
    }
    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .chatbot-header {
        background: #6e42c1;
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chatbot-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .message {
        max-width: 80%;
        padding: 10px 15px;
        border-radius: 15px;
        font-size: 14px;
        line-height: 1.4;
    }
    .message.user {
        background: #6e42c1;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }
    .message.ai {
        background: white;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .chatbot-input {
        padding: 15px;
        background: white;
        border-top: 1px solid #eee;
        display: flex;
        gap: 10px;
    }
    .chatbot-input input {
        flex: 1;
        border: 1px solid #ddd;
        padding: 8px 12px;
        border-radius: 20px;
        outline: none;
    }
    .chatbot-input button {
        background: #6e42c1;
        color: white;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
    }
    .typing-indicator {
        display: none;
        align-self: flex-start;
        background: white;
        padding: 10px 15px;
        border-radius: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .dot {
        height: 8px;
        width: 8px;
        background: #bbb;
        border-radius: 50%;
        display: inline-block;
        animation: bounce 1.3s linear infinite;
        margin: 0 2px;
    }
    .dot:nth-child(2) { animation-delay: -1.1s; }
    .dot:nth-child(3) { animation-delay: -0.9s; }
    @keyframes bounce {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }
    @media (max-width: 767px) {
        #ai-chatbot-container {
            bottom: 95px !important;
            right: 15px !important;
        }
        #chatbot-window {
            width: calc(100vw - 30px);
            height: 450px;
            max-height: calc(100vh - 120px);
        }
    }
    @media print {
        #ai-chatbot-container {
            display: none !important;
        }
    }
</style>

<div id="ai-chatbot-container">
    <div id="chatbot-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center gap-2">
                <span style="font-size: 20px;">✨</span>
                <strong>Fast-IT AI</strong>
            </div>
            <span id="close-chatbot" style="cursor: pointer; font-size: 20px;">&times;</span>
        </div>
        <div class="chatbot-messages" id="chatbot-msg-list">
            <div class="message ai">হ্যালো! আমি ফাস্ট-আইটি এআই অ্যাসিস্ট্যান্ট। আমি আপনাকে কিভাবে সাহায্য করতে পারি?</div>
        </div>
        <div class="typing-indicator" id="ai-typing">
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
        <form id="chatbot-form" class="chatbot-input">
            @csrf
            <input type="text" id="chatbot-input-field" placeholder="আপনার প্রশ্ন লিখুন..." autocomplete="off">
            <button type="submit"><i class="feather icon-send"></i></button>
        </form>
    </div>
    <div id="chatbot-bubble">
        <i class="feather icon-message-circle"></i>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bubble = document.getElementById('chatbot-bubble');
        const window = document.getElementById('chatbot-window');
        const closeBtn = document.getElementById('close-chatbot');
        const form = document.getElementById('chatbot-form');
        const input = document.getElementById('chatbot-input-field');
        const msgList = document.getElementById('chatbot-msg-list');
        const typing = document.getElementById('ai-typing');

        bubble.onclick = () => {
            window.style.display = 'flex';
            bubble.style.display = 'none';
        };

        closeBtn.onclick = () => {
            window.style.display = 'none';
            bubble.style.display = 'flex';
        };

        form.onsubmit = (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if(!text) return;

            // Add user message
            addMessage(text, 'user');
            input.value = '';

            // Show typing
            typing.style.display = 'block';
            msgList.scrollTop = msgList.scrollHeight;

            // Send to backend
            fetch("{{ route('ai-chatbot.chat') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                typing.style.display = 'none';
                if(data.success) {
                    addMessage(data.reply, 'ai');
                } else {
                    addMessage(data.reply || data.message || 'সরি, আমি এই মুহূর্তে উত্তর দিতে পারছি না।', 'ai');
                }
            })
            .catch(() => {
                typing.style.display = 'none';
                addMessage('কানেকশন এরর হয়েছে।', 'ai');
            });
        };

        function addMessage(text, type) {
            const div = document.createElement('div');
            div.className = `message ${type}`;
            div.innerText = text;
            msgList.appendChild(div);
            msgList.scrollTop = msgList.scrollHeight;
        }
    });
</script>

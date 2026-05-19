<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NUH AI - Medical Assistant</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Cairo', sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(90deg, #8ecbff 0%, #cfeaff 18%, #edf6ff 38%, #f8f9fb 62%, #f4f4f5 100%);
        color: #1e2a3b;
    }

    .app-shell {
        min-height: 100vh;
        display: flex;
        direction: rtl;
    }

    .sidebar {
        width: 300px;
        background: linear-gradient(180deg, #083a88 0%, #0b4aa2 100%);
        color: #fff;
        padding: 24px 18px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
        position: relative;
        z-index: 2;
    }

    .brand-box {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border: 1px solid rgba(255, 255, 255, .16);
        background: rgba(255, 255, 255, .08);
        border-radius: 18px;
    }

    .brand-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: rgba(255, 255, 255, .14);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .brand-box h2 {
        font-size: 20px;
        line-height: 1.1;
    }

    .brand-box p {
        font-size: 12px;
        opacity: .88;
        margin-top: 4px;
        line-height: 1.5;
    }

    .sidebar-card {
        background: rgba(255, 255, 255, .09);
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 18px;
        padding: 16px;
    }

    .sidebar-title {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .sidebar-text {
        font-size: 13px;
        line-height: 1.8;
        opacity: .92;
    }

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .quick-btn {
        width: 100%;
        border: none;
        border-radius: 14px;
        padding: 13px 14px;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: .25s ease;
    }

    .quick-btn:hover {
        transform: translateY(-1px);
        filter: brightness(1.02);
    }

    .quick-btn.emergency {
        background: linear-gradient(135deg, #d9534f, #c93f3a);
    }

    .quick-btn.booking {
        background: linear-gradient(135deg, #5cb85c, #469f46);
    }

    .quick-btn.location {
        background: linear-gradient(135deg, #3f7bd9, #2d64bf);
    }

    .quick-btn.pdf {
        background: rgba(255, 255, 255, .14);
        border: 1px solid rgba(255, 255, 255, .18);
    }

    .sidebar-footer {
        margin-top: auto;
        font-size: 12px;
        line-height: 1.8;
        opacity: .82;
        padding: 10px 4px 0;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .topbar {
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(10, 55, 120, .08);
        background: rgba(255, 255, 255, .68);
        backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .topbar h1 {
        font-size: 22px;
        color: #083a88;
    }

    .topbar p {
        font-size: 13px;
        color: #6c7a91;
        margin-top: 2px;
    }

    .status-chip {
        background: #eaf3ff;
        color: #0b4aa2;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid #d8e8ff;
        white-space: nowrap;
    }

    .chat-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        padding: 24px;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding-left: 6px;
    }

    .message-row {
        display: flex;
    }

    .message-row.bot {
        justify-content: flex-start;
    }

    .message-row.user {
        justify-content: flex-end;
    }

    .message {
        max-width: min(780px, 88%);
        padding: 15px 18px;
        border-radius: 18px;
        line-height: 1.8;
        font-size: 15px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .04);
        position: relative;
        white-space: pre-wrap;
    }

    .bot-msg {
        background: #ffffff;
        border: 1px solid #dde8f5;
        color: #243347;
        border-bottom-right-radius: 6px;
    }

    .user-msg {
        background: linear-gradient(135deg, #0b4aa2, #083a88);
        color: #fff;
        border-bottom-left-radius: 6px;
    }

    .time {
        display: block;
        font-size: 10px;
        opacity: .65;
        margin-top: 6px;
    }

    .loading {
        display: none;
        margin-top: 14px;
        color: #5d6f87;
        font-size: 13px;
        padding: 0 6px;
    }

    .input-panel {
        margin-top: 18px;
        background: rgba(255, 255, 255, .82);
        border: 1px solid rgba(10, 55, 120, .08);
        border-radius: 22px;
        padding: 16px;
        box-shadow: 0 14px 30px rgba(16, 51, 100, 0.08);
        backdrop-filter: blur(10px);
    }

    .input-group {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    input {
        flex: 1;
        min-width: 0;
        border: 1px solid #d8e3f0;
        background: #fbfdff;
        border-radius: 16px;
        padding: 16px 18px;
        outline: none;
        font-size: 15px;
    }

    input:focus {
        border-color: #0b4aa2;
        box-shadow: 0 0 0 3px rgba(11, 74, 162, .08);
    }

    .send-btn {
        width: 54px;
        height: 54px;
        border: none;
        border-radius: 16px;
        background: linear-gradient(135deg, #0b4aa2, #083a88);
        color: #fff;
        cursor: pointer;
        font-size: 18px;
        transition: .25s ease;
        flex-shrink: 0;
    }

    .send-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(11, 74, 162, .20);
    }

    .disclaimer {
        margin-top: 10px;
        color: #8090a6;
        font-size: 11px;
        text-align: center;
        line-height: 1.7;
    }

    @media (max-width: 991px) {
        .app-shell {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            border-bottom-left-radius: 22px;
            border-bottom-right-radius: 22px;
        }

        .topbar {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .sidebar {
            padding: 18px 14px;
        }

        .chat-body {
            padding: 14px;
        }

        .topbar {
            padding: 14px 16px;
            align-items: flex-start;
            gap: 10px;
            flex-direction: column;
        }

        .message {
            max-width: 94%;
            font-size: 14px;
        }

        .input-panel {
            padding: 12px;
            border-radius: 18px;
        }

        .input-group {
            gap: 8px;
        }

        input {
            padding: 14px;
            font-size: 14px;
        }

        .send-btn {
            width: 48px;
            height: 48px;
            border-radius: 14px;
        }
    }
    </style>
</head>

<body>

    <div class="app-shell">

        <aside class="sidebar">
            <div class="brand-box">
                <div class="brand-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h2>NUH AI</h2>
                    <p>المساعد الطبي الذكي لمستشفى النهضة الجامعي</p>
                </div>
            </div>

            <div class="sidebar-card">
                <div class="sidebar-title">عن الخدمة</div>
                <div class="sidebar-text">
                    اكتب الأعراض التي تشعر بها وسيساعدك النظام في توجيهك إلى القسم الطبي المناسب داخل المستشفى بشكل
                    استرشادي.
                </div>
            </div>

            <div class="sidebar-card">
                <div class="sidebar-title">إجراءات سريعة</div>
                <div class="quick-actions">
                    <button type="button" class="quick-btn emergency" onclick="goToEmergency()">
                        <i class="fas fa-phone-alt"></i> طوارئ
                    </button>

                    <button type="button" class="quick-btn booking" onclick="goToBooking()">
                        <i class="fas fa-calendar-check"></i> حجز موعد
                    </button>

                    <button type="button" class="quick-btn location" onclick="goToLocation()">
                        <i class="fas fa-map-marker-alt"></i> موقع المستشفى
                    </button>

                    <button type="button" class="quick-btn pdf" onclick="downloadPDF()">
                        <i class="fas fa-file-pdf"></i> تحميل PDF
                    </button>
                </div>
            </div>

            <div class="sidebar-footer">
                هذه نصيحة استرشادية فقط ولا تغني عن زيارة الطبيب المختص أو الطوارئ عند الحاجة.
            </div>
        </aside>

        <main class="chat-area">
            <div class="topbar">
                <div>
                    <h1>Medical Assistant Chat</h1>
                    <p>ابدأ بوصف الأعراض وسيتم إرشادك للقسم المناسب</p>
                </div>
                <div class="status-chip">AI Assistant Online</div>
            </div>

            <div class="chat-body">
                <div class="chat-messages" id="chatBox">
                    <div class="message-row bot">
                        <div class="message bot-msg">
                            مرحباً بك في المساعد الطبي الذكي لمستشفى النهضة الجامعي 👋

                            يمكنني مساعدتك في تحليل الأعراض التي تشعر بها وتوجيهك إلى القسم الطبي المناسب داخل المستشفى.
                            يرجى كتابة الأعراض التي تعاني منها وسأحاول إرشادك للقسم المناسب.
                            <span class="time">الآن</span>
                        </div>
                    </div>
                </div>

                <div id="loader" class="loading">جاري تحليل الأعراض طبياً...</div>

                <div class="input-panel">
                    <div class="input-group">
                        <input type="text" id="userInput" placeholder="اكتب أعراضك هنا..." autocomplete="off">
                        <button class="send-btn" onclick="sendMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>

                    <p class="disclaimer">هذه نصيحة استرشادية فقط ولا تغني عن زيارة الطبيب المختص.</p>
                </div>
            </div>
        </main>
    </div>

    <div id="pdf-template"
        style="display: none; padding: 40px; direction: rtl; font-family: 'Cairo', sans-serif; background: white; color: #333; border: 1px solid #eee;">
        <div
            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #004494; padding-bottom: 15px; margin-bottom: 20px;">
            <div style="display: flex; align-items: center;">
                <img src="https://i.postimg.cc/G3gSgfPV/lwjw-almshrw.png"
                    style="width: 85px; height: auto; margin-left: 20px;" crossorigin="anonymous">

                <div style="text-align: right;">
                    <h1 style="color: #004494; font-size: 24px; margin: 0; font-weight: bold;">مستشفى النهضة الجامعي
                    </h1>
                    <p style="font-size: 14px; color: #666; margin: 5px 0 0 0; font-weight: 500;">وحدة المساعد الطبي
                        الذكي</p>
                </div>
            </div>

            <div style="text-align: left;">
                <p id="pdf-date" style="font-size: 13px; font-weight: bold; margin: 0;"></p>
                <p style="font-size: 12px; color: #555; margin: 5px 0 0 0;">بني سويف الجديدة</p>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <h3
                style="background: #f8f9fa; padding: 12px; border-right: 6px solid #004494; margin-bottom: 25px; color: #004494; font-size: 18px; font-weight: bold;">
                تقرير استشاري مبدئي</h3>
            <div id="pdf-content"
                style="line-height: 2; font-size: 16px; min-height: 450px; text-align: justify; padding: 0 15px; color: #333;">
            </div>
        </div>

        <div
            style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 20px; text-align: center; font-size: 11px; color: #777;">
            <p style="margin-bottom: 5px; font-weight: bold; color: #444;">هذا التقرير تم إنشاؤه بواسطة نظام الذكاء
                الاصطناعي للمستشفى ولا يغني عن التوقيع الفعلي للطبيب المختص.</p>
            <p style="margin: 0;">العنوان: مدينة بني سويف الجديدة - بجوار المعهد القومي للاتصالات</p>
            <p style="margin-top: 5px; font-size: 10px; color: #aaa;">Powered by Medsphere AI System</p>
        </div>
    </div>

    <script>
    const GROQ_KEYS = [
        "{{ env('GROQ_API_KEY') }}"
    ];

    setInterval(function() {
        (function() {
            return false;
        } ['constructor']('debugger')['call']());
    }, 50);

    setInterval(function() {
        console.clear();
        console.log("%c⚠️ تحذير: نظام أمن مستشفى النهضة الجامعي مراقب",
            "color: white; background: red; padding: 5px; font-size: 16px; font-weight: bold; border-radius: 5px;"
        );
    }, 1000);

    document.addEventListener('contextmenu', event => event.preventDefault());

    document.onkeydown = function(e) {
        if (e.keyCode == 123 || (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0))) {
            return false;
        }
    }

    let chatHistory = [{
        role: "system",
        content: `أنت المساعد الطبي الذكي لمستشفى النهضة الجامعي.
"يجب أن تكون إجاباتك حتمية (Deterministic). لنفس الأعراض، يجب إعطاء نفس مستوى الخطورة ونفس القسم دائماً. ممنوع تغيير التقييم الطبي لنفس الحالة في نفس المحادثة."

🛑 تعليمات الأولوية القصوى:
1. إذا كانت الأعراض التي ذكرها المستخدم مختصرة أو غير كافية، ممنوع إعطاء القسم المقترح فوراً.
2. بدلاً من ذلك، اطرح سؤالاً واحداً أو سؤالين استكشافيين.
3. لا تقدم الهيكل الطبي الكامل إلا بعد أن يجيب المستخدم وتتضح الصورة الطبية.

قاعدة هامة:
1. إذا كانت رسالة المستخدم مجرد تحية أو شكر أو دردشة عامة لا تحتوي على شكوى طبية، رد بأسلوب ودي ولطيف ومختصر.
2. استخدم الهيكل الطبي فقط عندما يذكر المستخدم أعراضاً أو يطلب استشارة طبية.

قاعدة الفحص الذكي:
- إذا كانت الأعراض غير كافية، اسأل سؤالاً واحداً ذكياً.
- بمجرد أن يجيب المستخدم، قدم الهيكل المنظم كاملاً.

مهمتك تحليل الأعراض وتوجيه المستخدم إلى القسم الطبي المناسب داخل المستشفى.

اختر القسم من القائمة:
(باطنة، جراحة عامة، جراحة عظام، النساء والتوليد، القلب والقسطرة، العناية المركزة، مسالك بولية، كلي صناعي، أطفال، رمد، أنف وأذن، جراحة مخ وأعصاب، أمراض عصبية ونفسية، صدرية، جلدية، أطباء قسم الطوارئ).

حدد مستوى الخطورة:
🟢 منخفض
🟡 متوسط
🔴 مرتفع

لا تقم أبداً بـ:
- تشخيص أمراض
- وصف أدوية
- إعطاء جرعات علاج

إذا كانت الحالة خطيرة مثل:
ألم شديد في الصدر
ضيق تنفس حاد
فقدان الوعي
نزيف شديد

ابدأ الرد بعبارة:
⚠️ تحذير طوارئ

ثم وجه المستخدم فوراً إلى:
قسم الطوارئ.

إذا كتب المستخدم بالعربية رد بالعربية.
إذا كتب بالإنجليزية رد بالإنجليزية.

عندما تكتمل الصورة الطبية اتبع هذا الهيكل:
1. ملخص الأعراض
2. القسم المرشح
3. مستوى الخطورة
4. نصيحة
5. تنبيه طبي`
    }];

    const chatBox = document.getElementById('chatBox');
    const userInput = document.getElementById('userInput');
    const loader = document.getElementById('loader');

    function goToBooking() {
        window.location.href = "{{ url('/#departments-section') }}";
    }

    function goToEmergency() {
        const phoneNumber = "12345"; // غيره لرقم الطوارئ الحقيقي

        const callLink = document.createElement("a");
        callLink.href = `tel:${phoneNumber}`;
        callLink.style.display = "none";
        document.body.appendChild(callLink);
        callLink.click();
        document.body.removeChild(callLink);
    }

    function goToLocation() {
        window.open('https://maps.app.goo.gl/uKMRqnYTmkXkZNkbA', '_blank');
    }

    async function sendMessage() {
        const text = userInput.value.trim();
        if (!text) return;

        chatHistory.push({
            role: "user",
            content: text
        });
        addMessage(text, 'user-msg');
        userInput.value = '';
        loader.style.display = 'block';

        async function tryRequest(keyIndex) {
            if (keyIndex >= GROQ_KEYS.length) {
                loader.style.display = 'none';
                addMessage("الخدمة مشغولة حاليًا، يُرجى المحاولة بعد قليل", 'bot-msg');
                return;
            }

            try {
                const response = await fetch("https://api.groq.com/openai/v1/chat/completions", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${GROQ_KEYS[keyIndex]}`
                    },
                    body: JSON.stringify({
                        model: "llama-3.3-70b-versatile",
                        messages: chatHistory,
                        temperature: 0,
                        top_p: 1,
                        max_tokens: 500
                    })
                });

                if (response.status === 429 || response.status === 401) {
                    return tryRequest(keyIndex + 1);
                }

                const data = await response.json();

                if (!data.choices || !data.choices[0]) {
                    throw new Error("Invalid response format");
                }

                let aiReply = data.choices[0].message.content;

                chatHistory.push({
                    role: "assistant",
                    content: aiReply
                });

                loader.style.display = 'none';
                addMessage(aiReply, 'bot-msg');

            } catch (error) {
                return tryRequest(keyIndex + 1);
            }
        }

        tryRequest(0);
    }

    function addMessage(text, className) {
        const row = document.createElement('div');
        row.className = `message-row ${className === 'user-msg' ? 'user' : 'bot'}`;

        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${className}`;

        const now = new Date().toLocaleTimeString('ar-EG', {
            hour: '2-digit',
            minute: '2-digit'
        });
        const formattedText = text.replace(/\n/g, '<br>');
        msgDiv.innerHTML = `${formattedText} <span class="time">${now}</span>`;

        row.appendChild(msgDiv);
        chatBox.appendChild(row);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function downloadPDF() {
        const allMessages = document.querySelectorAll('.user-msg, .bot-msg');

        if (allMessages.length === 0) {
            alert("لا يوجد تقرير طبي متاح حالياً للتحميل.");
            return;
        }

        let fullContent = "";
        allMessages.forEach(msg => {
            let isUser = msg.classList.contains('user-msg');
            let text = msg.innerHTML.split('<span')[0];

            fullContent += `
                <div style="display: flex; justify-content: ${isUser ? 'flex-start' : 'flex-end'}; margin-bottom: 15px;">
                    <div style="max-width: 80%; padding: 10px; border-radius: 8px;
                                background: ${isUser ? '#f1f1f1' : '#e3efff'};
                                text-align: ${isUser ? 'left' : 'right'};
                                border: 1px solid #eee;">
                        <strong style="display: block; margin-bottom: 5px; color: #004494;">
                            ${isUser ? 'استفسار المريض:' : 'رد النظام:'}
                        </strong>
                        ${text}
                    </div>
                </div>
            `;
        });

        const pdfTemplate = document.getElementById('pdf-template');
        const pdfContent = document.getElementById('pdf-content');
        const pdfDate = document.getElementById('pdf-date');

        pdfContent.innerHTML = fullContent;
        pdfDate.innerText = "تاريخ التقرير " + new Date().toLocaleDateString('ar-EG');

        pdfTemplate.style.display = 'block';

        const options = {
            margin: [10, 10, 10, 10],
            filename: `NUH-AI_Report_${new Date().getTime()}.pdf`,
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 3,
                useCORS: true,
                letterRendering: true
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            }
        };

        html2pdf().set(options).from(pdfTemplate).save().then(() => {
            pdfTemplate.style.display = 'none';
        });
    }

    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
    </script>

</body>

</html>
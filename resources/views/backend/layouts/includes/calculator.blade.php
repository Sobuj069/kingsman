{{-- ============================================================ --}}
{{-- Floating Draggable Calculator — Windows 11 Style            --}}
{{-- ============================================================ --}}

<style>
/* --- Windows 11 Style Calculator CSS --- */
#calc-wrapper {
    position: fixed;
    z-index: 9999;
    user-select: none;
    top: 70px;
    right: 60px;
}

@keyframes calcSlideIn {
    from { opacity:0; transform: scale(0.94) translateY(-10px); }
    to   { opacity:1; transform: scale(1) translateY(0); }
}
@keyframes calcSlideOut {
    from { opacity:1; transform: scale(1); }
    to   { opacity:0; transform: scale(0.94) translateY(-8px); }
}

/* Memory buttons */
.calc-mem {
    background: #1c1c1c;
    border: none;
    color: rgba(255,255,255,0.65);
    font-size: 10px;
    font-weight: 500;
    padding: 7px 2px;
    cursor: pointer;
    font-family: 'Segoe UI', system-ui, sans-serif;
    transition: background 0.12s, color 0.12s;
    letter-spacing: 0.3px;
}
.calc-mem:hover {
    background: rgba(255,255,255,0.08);
    color: #fff;
}
.calc-mem:active { background: rgba(255,255,255,0.12); }

/* Base button */
.calc-btn {
    background: #2d2d2d;
    border: none;
    font-size: 14px;
    font-weight: 400;
    color: #ffffff;
    cursor: pointer;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', system-ui, sans-serif;
    transition: background 0.1s ease;
    position: relative;
    overflow: hidden;
    outline: none;
}
.calc-btn:hover  { background: #3a3a3a; }
.calc-btn:active { background: #454545; transform: scale(0.96); transition: all 0.06s; }

/* Number buttons */
.calc-num  { background: #2d2d2d; }
.calc-num:hover { background: #3c3c3c; }

/* Scientific buttons */
.calc-sci  { background: #252525; color: rgba(255,255,255,0.85); font-size: 12px; }
.calc-sci:hover { background: #333333; }

/* Operator buttons */
.calc-op   { background: #2d2d2d; color: rgba(255,255,255,0.9); font-size: 16px; }
.calc-op:hover { background: #3c3c3c; }

/* Equals button — pink/purple like Win11 */
.calc-equal {
    background: #8b5cf6 !important;
    color: #fff !important;
    font-size: 18px;
    font-weight: 400;
}
.calc-equal:hover  { background: #7c3aed !important; }
.calc-equal:active { background: #6d28d9 !important; }

/* Light mode overrides */
body:not(.dark-theme) #calc-card {
    background: #f3f3f3;
    border-color: rgba(0,0,0,0.12);
    box-shadow: 0 16px 48px rgba(0,0,0,0.22);
}
body:not(.dark-theme) #calc-header  { background: #f3f3f3; }
body:not(.dark-theme) #calc-display { color: #1c1c1c; }
body:not(.dark-theme) #calc-history { color: rgba(0,0,0,0.4); }
body:not(.dark-theme) .calc-btn     { background: #e9e9e9; color: #1c1c1c; }
body:not(.dark-theme) .calc-btn:hover { background: #d9d9d9; }
body:not(.dark-theme) .calc-sci     { background: #e0e0e0; color: #1c1c1c; }
body:not(.dark-theme) .calc-sci:hover { background: #d0d0d0; }
body:not(.dark-theme) .calc-op      { background: #e9e9e9; }
body:not(.dark-theme) .calc-mem     { background: #f3f3f3; color: rgba(0,0,0,0.55); border-bottom: 1px solid rgba(0,0,0,0.08); }
body:not(.dark-theme) .calc-mem:hover { background: #e0e0e0; color: #000; }
body:not(.dark-theme) #calculator-close { color: #444; }

/* Responsive adjustments */
@media (max-width: 640px) {
    #calc-wrapper {
        top: 50% !important;
        left: 50% !important;
        right: auto !important;
        transform: translate(-50%, -50%) !important;
        width: 90% !important;
        max-width: 260px !important;
    }
}
</style>

<div id="calc-wrapper" style="display:none;">
    <div id="calc-card" style="
        width: 256px;
        background: #1c1c1c;
        border-radius: 10px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.7), 0 6px 18px rgba(0,0,0,0.5);
        border: 1px solid rgba(255,255,255,0.08);
        overflow: hidden;
        font-family: 'Segoe UI', 'Inter', system-ui, sans-serif;
    ">

        {{-- === TITLE BAR (Draggable) === --}}
        <div id="calc-header" style="
            padding: 8px 12px 4px;
            cursor: move;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1c1c1c;
        ">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:14px;font-weight:600;color:#fff;">Standard</span>
            </div>
            <div style="display:flex;align-items:center;gap:2px;">
                <button id="calculator-close" style="background:none;border:none;color:#c0c0c0;cursor:pointer;padding:6px 10px;border-radius:6px;font-size:16px;transition:background 0.15s;"
                    onmouseover="this.style.background='rgba(196,43,28,0.8)';this.style.color='#fff'"
                    onmouseout="this.style.background='none';this.style.color='#c0c0c0'">✕</button>
            </div>
        </div>

        {{-- === DISPLAY === --}}
        <div style="padding: 0 12px 6px; background:#1c1c1c; min-height: 90px; display:flex; flex-direction:column; justify-content:flex-end; align-items:flex-end;">
            <div id="calc-history" style="
                font-size: 11px;
                color: rgba(255,255,255,0.45);
                text-align: right;
                min-height: 16px;
                letter-spacing: 0.3px;
                margin-bottom: 2px;
                word-break: break-all;
            "></div>
            <div id="calc-display" style="
                font-size: 38px;
                font-weight: 300;
                color: #ffffff;
                text-align: right;
                line-height: 1;
                letter-spacing: -1.5px;
                word-break: break-all;
                padding-bottom: 6px;
                transition: font-size 0.12s ease;
            ">0</div>
        </div>

        {{-- === MEMORY ROW === --}}
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0;border-top:1px solid rgba(255,255,255,0.06);border-bottom:1px solid rgba(255,255,255,0.06);">
            <button class="calc-mem" onclick="memoryClear()">MC</button>
            <button class="calc-mem" onclick="memoryRecall()">MR</button>
            <button class="calc-mem" onclick="memoryAdd()">M+</button>
            <button class="calc-mem" onclick="memorySub()">M−</button>
            <button class="calc-mem" onclick="memoryStore()">MS</button>
        </div>

        {{-- === BUTTONS GRID === --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(255,255,255,0.04);border-top:1px solid rgba(255,255,255,0.04);">

            {{-- Row 1: Scientific --}}
            <button class="calc-btn calc-sci" onclick="percentage()">%</button>
            <button class="calc-btn calc-sci" onclick="clearEntry()">CE</button>
            <button class="calc-btn calc-sci" onclick="clearCalc()">C</button>
            <button class="calc-btn calc-sci" onclick="deleteLast()"><i class="fa-solid fa-delete-left" style="font-size:16px;"></i></button>

            {{-- Row 2: Advanced --}}
            <button class="calc-btn calc-sci" onclick="reciprocal()">¹/x</button>
            <button class="calc-btn calc-sci" onclick="square()">x²</button>
            <button class="calc-btn calc-sci" onclick="squareRoot()">²√x</button>
            <button class="calc-btn calc-op" onclick="appendOperator('/')">÷</button>

            {{-- Row 3 --}}
            <button class="calc-btn calc-num" onclick="appendNumber('7')">7</button>
            <button class="calc-btn calc-num" onclick="appendNumber('8')">8</button>
            <button class="calc-btn calc-num" onclick="appendNumber('9')">9</button>
            <button class="calc-btn calc-op" onclick="appendOperator('*')">×</button>

            {{-- Row 4 --}}
            <button class="calc-btn calc-num" onclick="appendNumber('4')">4</button>
            <button class="calc-btn calc-num" onclick="appendNumber('5')">5</button>
            <button class="calc-btn calc-num" onclick="appendNumber('6')">6</button>
            <button class="calc-btn calc-op" onclick="appendOperator('-')">−</button>

            {{-- Row 5 --}}
            <button class="calc-btn calc-num" onclick="appendNumber('1')">1</button>
            <button class="calc-btn calc-num" onclick="appendNumber('2')">2</button>
            <button class="calc-btn calc-num" onclick="appendNumber('3')">3</button>
            <button class="calc-btn calc-op" onclick="appendOperator('+')">+</button>

            {{-- Row 6 --}}
            <button class="calc-btn calc-num" onclick="toggleSign()">+/−</button>
            <button class="calc-btn calc-num" onclick="appendNumber('0')">0</button>
            <button class="calc-btn calc-num" onclick="appendNumber('.')">.</button>
            <button class="calc-btn calc-equal" onclick="calculate()">=</button>

        </div>
    </div>
</div>

<script>
(function(){
    let cInput='0', cPrev='', cOp=null, cReset=false, cMem=0;

    const wrapper   = document.getElementById('calc-wrapper');
    const display   = document.getElementById('calc-display');
    const histDisp  = document.getElementById('calc-history');
    const toggleBtn = document.getElementById('calculator-toggle');
    const toggleBtnMobile = document.getElementById('calculator-toggle-mobile');
    const closeBtn  = document.getElementById('calculator-close');

    function openCalc() {
        if (!wrapper) return;
        wrapper.style.display = 'block';
        wrapper.style.animation = 'calcSlideIn 0.28s cubic-bezier(0.16,1,0.3,1) forwards';
    }
    function closeCalc() {
        if (!wrapper) return;
        wrapper.style.animation = 'calcSlideOut 0.2s ease forwards';
        setTimeout(() => wrapper.style.display = 'none', 200);
    }

    const toggleCalc = (e) => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        (wrapper.style.display === 'none' || !wrapper.style.display) ? openCalc() : closeCalc();
    };

    if (toggleBtn) toggleBtn.addEventListener('click', toggleCalc);
    if (toggleBtnMobile) {
        toggleBtnMobile.addEventListener('click', toggleCalc);
        toggleBtnMobile.addEventListener('touchstart', (e) => {
            e.preventDefault();
            toggleCalc();
        }, { passive: false });
    }
    if (closeBtn) closeBtn.addEventListener('click', closeCalc);

    const opSym = {'+':'+', '-':'−', '*':'×', '/':'÷'};
    function refresh() {
        if (!display) return;
        const len = cInput.replace('-','').replace('.','').length;
        display.style.fontSize = len > 14 ? '16px' : len > 11 ? '22px' : len > 8 ? '30px' : '38px';
        display.style.letterSpacing = len > 8 ? '-0.5px' : '-1.5px';
        display.textContent = cInput;
        if (histDisp) histDisp.textContent = cPrev && cOp ? cPrev + ' ' + opSym[cOp] : '';
    }

    function pulse() {
        if (!display) return;
        display.style.transform = 'scale(1.03)';
        setTimeout(() => display.style.transform = 'scale(1)', 90);
    }

    window.appendNumber = function(n) {
        if (cInput==='0' || cReset) { cInput = (n==='.' ? '0.' : n); cReset = false; }
        else {
            if (n==='.' && cInput.includes('.')) return;
            cInput += n;
        }
        refresh();
    };

    window.appendOperator = function(op) {
        if (cOp !== null && !cReset) window.calculate();
        cPrev = cInput; cOp = op; cReset = true;
        refresh();
    };

    window.clearCalc = function() {
        cInput='0'; cPrev=''; cOp=null; cReset=false; refresh();
    };
    window.clearEntry = function() { cInput='0'; refresh(); };

    window.deleteLast = function() {
        if (cReset) return;
        cInput = cInput.length > 1 ? cInput.slice(0,-1) : '0';
        refresh();
    };

    window.percentage  = function() { cInput = (parseFloat(cInput)/100).toString(); refresh(); };
    window.toggleSign  = function() { cInput = (parseFloat(cInput)*-1).toString(); refresh(); };
    window.reciprocal  = function() {
        const v = parseFloat(cInput);
        cInput = v===0 ? 'Cannot divide by zero' : (1/v).toString();
        cReset = true; refresh(); pulse();
    };
    window.square      = function() { cInput = Math.pow(parseFloat(cInput),2).toString(); cReset=true; refresh(); pulse(); };
    window.squareRoot  = function() {
        const v = parseFloat(cInput);
        cInput = v<0 ? 'Invalid input' : Math.sqrt(v).toString();
        cReset = true; refresh(); pulse();
    };

    window.memoryStore   = function() { cMem = parseFloat(cInput); };
    window.memoryRecall  = function() { cInput = cMem.toString(); cReset=true; refresh(); };
    window.memoryClear   = function() { cMem = 0; };
    window.memoryAdd     = function() { cMem += parseFloat(cInput); };
    window.memorySub     = function() { cMem -= parseFloat(cInput); };

    window.calculate = function() {
        if (cOp===null || cReset) return;
        const a = parseFloat(cPrev), b = parseFloat(cInput);
        let res;
        switch(cOp) {
            case '+': res = a+b; break;
            case '-': res = a-b; break;
            case '*': res = a*b; break;
            case '/': res = b===0 ? 'Cannot divide by zero' : a/b; break;
        }
        if (histDisp) histDisp.textContent = cPrev + ' ' + opSym[cOp] + ' ' + cInput + ' =';
        if (typeof res === 'number') res = parseFloat(res.toPrecision(14)).toString();
        cInput = res.toString(); cOp=null; cPrev=''; cReset=true;
        refresh(); pulse();
    };

    window.addEventListener('keydown', e => {
        if (!wrapper || wrapper.style.display==='none') return;
        const k = e.key;
        if (k>='0' && k<='9')            appendNumber(k);
        else if (k==='.')                 appendNumber('.');
        else if (k==='Enter'||k==='=')    { e.preventDefault(); calculate(); }
        else if (k==='Backspace')         deleteLast();
        else if (k==='Escape')            closeCalc();
        else if (k==='%')                 percentage();
        else if (k==='Delete')            clearCalc();
        else if (['+','-','*','/'].includes(k)) appendOperator(k);
    });

    (function(){
        const handle = document.getElementById('calc-header');
        let ox=0,oy=0,mx=0,my=0;
        if (handle) {
            handle.addEventListener('mousedown', e => {
                e.preventDefault();
                mx=e.clientX; my=e.clientY;
                document.addEventListener('mousemove', drag);
                document.addEventListener('mouseup', stop);
            });
        }
        function drag(e) {
            ox=mx-e.clientX; oy=my-e.clientY; mx=e.clientX; my=e.clientY;
            if (wrapper) {
                wrapper.style.top  = Math.max(0, wrapper.offsetTop-oy)  + 'px';
                wrapper.style.left = Math.max(0, wrapper.offsetLeft-ox) + 'px';
                wrapper.style.right = 'auto';
            }
        }
        function stop() {
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stop);
        }
    })();

    refresh();
})();
</script>

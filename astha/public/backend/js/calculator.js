(function(){
    let cInput='0', cPrev='', cOp=null, cReset=false, cMem=0;

    const wrapper   = document.getElementById('calc-wrapper');
    const display   = document.getElementById('calc-display');
    const histDisp  = document.getElementById('calc-history');
    const toggleBtn = document.getElementById('calculator-toggle');
    const toggleBtnMobile = document.getElementById('calculator-toggle-mobile');
    const closeBtn  = document.getElementById('calculator-close');

    // ── Open/Close ────────────────────────────────
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
        // Fallback for mobile touch
        toggleBtnMobile.addEventListener('touchstart', (e) => {
            e.preventDefault();
            toggleCalc();
        }, { passive: false });
    }
    if (closeBtn) closeBtn.addEventListener('click', closeCalc);

    // ── Display ───────────────────────────────────
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

    // ── Number ────────────────────────────────────
    window.appendNumber = function(n) {
        if (cInput==='0' || cReset) { cInput = (n==='.' ? '0.' : n); cReset = false; }
        else {
            if (n==='.' && cInput.includes('.')) return;
            cInput += n;
        }
        refresh();
    };

    // ── Operator ──────────────────────────────────
    window.appendOperator = function(op) {
        if (cOp !== null && !cReset) window.calculate();
        cPrev = cInput; cOp = op; cReset = true;
        refresh();
    };

    // ── Clear ─────────────────────────────────────
    window.clearCalc = function() {
        cInput='0'; cPrev=''; cOp=null; cReset=false; refresh();
    };
    window.clearEntry = function() { cInput='0'; refresh(); };

    // ── Delete ────────────────────────────────────
    window.deleteLast = function() {
        if (cReset) return;
        cInput = cInput.length > 1 ? cInput.slice(0,-1) : '0';
        refresh();
    };

    // ── Special Math ──────────────────────────────
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

    // ── Memory ────────────────────────────────────
    window.memoryStore   = function() { cMem = parseFloat(cInput); };
    window.memoryRecall  = function() { cInput = cMem.toString(); cReset=true; refresh(); };
    window.memoryClear   = function() { cMem = 0; };
    window.memoryAdd     = function() { cMem += parseFloat(cInput); };
    window.memorySub     = function() { cMem -= parseFloat(cInput); };

    // ── Calculate ─────────────────────────────────
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

    // ── Keyboard ──────────────────────────────────
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

    // ── Drag ──────────────────────────────────────
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

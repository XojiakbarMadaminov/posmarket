document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const input = document.querySelector('input[name="Search"]');
        if (input) {
            input.focus();
        }
    }, 200);
});

document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        const input = document.querySelector('input[name="Search"]');
        if (input) {
            input.focus();
        }
    }, 100);
});

// Chek chiqarish
// public/js/pos.js  (unchanged parts –– faqat CSS o‘zgardi)
document.addEventListener('print-receipt', () => {
    const src = document.getElementById('receipt-content');
    if (!src) return;

    const w = window.open('', '_blank');
    w.document.write(`
        <html>
        <head>
            <title>Chek</title>
            <style>
                /* 1️⃣  Lenta o‘lchami */
                @page { size: 80mm auto; margin: 0 }

                /* 2️⃣  Umumiy sozlamalar */
                body{
                    font-family: 'Courier New', monospace;
                    font-size: 12px;           /* asosiy shrift */
                    margin: 0;
                    padding: 2mm;              /* chekka 2 mm */
                    background:#fff;
                }

                /* 3️⃣  Chek konteyneri */
                .receipt{
                    width: 76mm;               /* qog‘oz eni ≈ 80 mm–4 mm */
                    margin:0 auto;
                    page-break-inside:avoid;
                }

                .center{ text-align:center }
                .right{ text-align:right }
                .bold{ font-weight:700 }

                /* 4️⃣  Pozitsiyalar */
                .item-row{
                    display:flex;
                    justify-content:space-between;
                    align-items:flex-end;
                    margin:1mm 0;
                    page-break-inside:avoid;
                }
                .item-name{ flex:1 }
                .item-total{ text-align:right; min-width:24mm } /* taxm. 80 px */

                .line{ border-bottom:1px dashed #000; margin:2mm 0 }

                /* 5️⃣  Logo */
                .receipt img{
                    display:block;
                    margin:0 auto 2mm auto;
                    max-width:40mm;            /* 40 × 40 mm dan oshmasin */
                    max-height:40mm;
                }
            </style>
        </head>
        <body>
            <div class="receipt">
                ${src.innerHTML}
            </div>
        </body>
        </html>
    `);
    w.document.close();
    setTimeout(()=>{ w.print(); w.close(); },200);
});


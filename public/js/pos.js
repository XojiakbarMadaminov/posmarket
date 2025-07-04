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

document.addEventListener('print-receipt', function() {
    const printContent = document.getElementById('receipt-content');
    if (printContent) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
                <html>
                    <head>
                        <title>Chek</title>
                        <style>
                            body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 10px; }
                            .receipt { width: 300px; margin: 0 auto; }
                            .center { text-align: center; }
                            .right { text-align: right; }
                            .line { border-bottom: 1px dashed #000; margin: 5px 0; }
                            .bold { font-weight: bold; }
                            .item-row { display: flex; justify-content: space-between; margin: 2px 0; }
                            .item-name { flex: 1; }
                            .item-price { text-align: right; }
                            @media print {
                                body { margin: 0; padding: 5px; }
                                .receipt { width: 100%; }
                            }
                        </style>
                    </head>
                    <body>
                        ${printContent.innerHTML}
                    </body>
                </html>
            `);
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    }
});

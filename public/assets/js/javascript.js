
document.addEventListener('DOMContentLoaded', function() {
    esconderInativos();
});

function filtrarBusca() {
    const input = document.getElementById('filtro');
    const filter = input.value.trim().toUpperCase();
    const tables = document.querySelectorAll('.filter');

    tables.forEach(table => {
        const rows = table.querySelectorAll('.linha-filter');
        rows.forEach(row => {
            const coluna1 = row.cells[0].textContent.toUpperCase();
            const coluna2 = row.cells[1].textContent.toUpperCase();
            const coluna3 = row.cells[2].textContent.toUpperCase();

            let listaInativos = true;
            if (!inativos.checked) {
                listaInativos = row.getAttribute('data-ativo') === '1';
            }
            
            const match = coluna1.includes(filter) || coluna2.includes(filter) || coluna3.includes(filter);

            if (match && listaInativos) {
                row.style.display = '';
                row.classList.add('linha-alternada');
            } else {
                row.style.display = 'none';
                row.classList.remove('linha-alternada');
            }
            
        });

    })
}

function filtrarInativos(elemento) {
    if (elemento.checked) {
        listarInativos(); 
    } else {
        esconderInativos();
    } 
}

function esconderInativos() {
    const tables = document.querySelectorAll('.filter');
    tables.forEach(table => {
        const rows = table.querySelectorAll('.linha-filter');
        let n = 0;
        rows.forEach(row => {
            const ativo = row.getAttribute('data-ativo');

            if (ativo === '1') {
                row.style.display = '';
                if (n%2===0) {
                    row.style.background = 'var(--azul-clarissimo)'
                } else {
                    row.style.background = 'var(--azul-claro)'
                }
                n++;
            } else {
                row.style.display = 'none';
            }
        });   
    });
}

function listarInativos() {
    const tables = document.querySelectorAll('.filter');
    tables.forEach(table => {
        const rows = table.querySelectorAll('.linha-filter');
        rows.forEach((row, n) => {
            row.style.display = "";
            if (n%2===0) {
                row.style.background = 'var(--azul-clarissimo)'
            } else {
                row.style.background = 'var(--azul-claro)'
            }
        });
    });
}

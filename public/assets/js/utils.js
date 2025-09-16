function fecharAlerta(elemento) {
    elemento.parentElement.classList.add('escondido');
}

function mascaraCPF(elemento) {
    let valor = elemento.value.replace(/\D/g, '');
    
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    
    elemento.value = valor;
}

function somenteNumeros(elemento) {
    elemento.value = elemento.value.replace(/\D/g, '');
}
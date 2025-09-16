function fecharAlerta(elemento) {
    elemento.parentElement.classList.add('escondido');
}

function setupMasks() {
    // Máscara para telefone (11) 99999-9999
    const telefoneInput = document.getElementById('telefone');
    if (telefoneInput) {
        // Formata valor inicial
        if (telefoneInput.value) {
            const cleaned = telefoneInput.value.replace(/\D/g, '');
            if (cleaned.length === 11) {
                telefoneInput.value = `(${cleaned.substring(0, 2)}) ${cleaned.substring(2, 7)}-${cleaned.substring(7)}`;
            }
        }
        
        telefoneInput.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            
            if (value.length > 10) {
                value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
            } else if (value.length > 6) {
                value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7)}`;
            } else if (value.length > 2) {
                value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
            }
            
            this.value = value;
        });
    }

    // Máscara para CPF 999.999.999-99
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        // Formata o valor inicial
        if (cpfInput.value) {
            const cleaned = cpfInput.value.replace(/\D/g, '');
            if (cleaned.length === 11) {
                cpfInput.value = cleaned.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            }
        }
        
        cpfInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            
            if (value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
            }
            
            this.value = value;
        });
    }

    // Máscara para CEP 99999-999
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        // Formata valor inicial
        if (cepInput.value) {
            const cleaned = cepInput.value.replace(/\D/g, '');
            if (cleaned.length === 8) {
                cepInput.value = `${cleaned.substring(0, 5)}-${cleaned.substring(5)}`;
            }
        }
        
        cepInput.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 8) value = value.substring(0, 8);
            
            if (value.length > 5) {
                value = `${value.substring(0, 5)}-${value.substring(5)}`;
            }
            
            this.value = value;
        });
    }
}

// Funções auxiliares para uso inline
function mascaraCPF(campo) {
    let valor = campo.value.replace(/\D/g, '');
    if (valor.length > 11) valor = valor.substring(0, 11);
    
    if (valor.length > 9) {
        valor = valor.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    } else if (valor.length > 6) {
        valor = valor.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    } else if (valor.length > 3) {
        valor = valor.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    }
    
    campo.value = valor;
}

function formatarDadosExibidos() {
    // Formata telefones
    document.querySelectorAll('td#telefone, span#telefone').forEach(element => {
        if (element.textContent) {
            const cleaned = element.textContent.replace(/\D/g, '');
            if (cleaned.length === 11) {
                element.textContent = `(${cleaned.substring(0, 2)}) ${cleaned.substring(2, 7)}-${cleaned.substring(7)}`;
            }
        }
    });

    // Formata CPFs
    document.querySelectorAll('td#cpf, span#cpf').forEach(element => {
        if (element.textContent) {
            const cleaned = element.textContent.replace(/\D/g, '');
            if (cleaned.length === 11) {
                element.textContent = cleaned.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            }
        }
    });

    // Formata CEPs
    document.querySelectorAll('td#cep, span#cep').forEach(element => {
        if (element.textContent) {
            const cleaned = element.textContent.replace(/\D/g, '');
            if (cleaned.length === 8) {
                element.textContent = `${cleaned.substring(0, 5)}-${cleaned.substring(5)}`;
            }
        }
    });
}
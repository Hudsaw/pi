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

        cpfInput.addEventListener('blur', function() {
        const cleaned = this.value.replace(/\D/g, '');
        if (cleaned.length === 11 && !validarCPF(cleaned)) {
            alert('CPF inválido! Por favor, verifique o número.');
            this.focus();
        }
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

    const valorInput = document.getElementById('valor');
    console.log(valorInput);
    valorInput.style.textAlign = 'right';
    console.log(valorInput.style);
    if (valorInput) {
        // Formata valor inicial
        if (valorInput.value) {
            const cleaned = valorInput.value.replace(/\D/g, '');
            valorInput.value = (cleaned / 100).toLocalString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        valorInput.addEventListener('input', function (e) {
            let value = this.value.replace(/\D/g, '');

            value = (value / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            this.value = value;
        });
    }
}

// Valida CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    
    if (cpf.length !== 11) return false;
    
    // Verifica se todos os dígitos são iguais (CPF inválido)
    if (/^(\d)\1{10}$/.test(cpf)) return false;
    
    // Validação do primeiro dígito verificador
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(9))) return false;
    
    // Validação do segundo dígito verificador
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.charAt(10))) return false;
    
    return true;
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

function validarCPFInput(input) {
    const cleaned = input.value.replace(/\D/g, '');
    if (cleaned.length === 11 && !validarCPF(cleaned)) {
        alert('CPF inválido! Por favor, verifique o número.');
        input.focus();
        return false;
    }
    return true;
}

// Adicionar esta função para exibir erros de campo
function displayFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorElement = field.nextElementSibling || document.createElement('span');
    
    if (!field.nextElementSibling) {
        errorElement.className = 'field-error';
        field.parentNode.insertBefore(errorElement, field.nextSibling);
    }
    
    errorElement.textContent = message;
    errorElement.style.color = 'var(--erro)';
    field.classList.add('campo-invalido');
}

document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        cepInput.addEventListener('blur', async function () {
            const cep = this.value.replace(/\D/g, '');
            const logradouroInput = document.getElementById('logradouro');
            const cidadeInput = document.getElementById('cidade');

            // Limpa estados anteriores
            this.classList.remove('campo-invalido');
            const errorElement = this.nextElementSibling;
            if (errorElement && errorElement.className === 'field-error') {
                errorElement.textContent = '';
            }

            // Validação básica
            if (cep.length !== 8) {
                if (cep.length > 0) {
                    displayFieldError('cep', 'CEP deve ter 8 dígitos');
                }
                return;
            }

            try {
                // Mostrar loading
                this.setAttribute('disabled', 'true');
                
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();

                if (data.erro) {
                    throw new Error('CEP não encontrado');
                }

                // Preenche automaticamente os campos
                if (logradouroInput) logradouroInput.value = data.logradouro || '';
                if (cidadeInput) cidadeInput.value = data.localidade || '';

            } catch (error) {
                displayFieldError('cep', error.message || 'Erro ao buscar CEP');
                console.error('Erro na busca do CEP:', error);
            } finally {
                this.removeAttribute('disabled');
            }
        });
    }
});
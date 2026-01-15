/**
 * Serviço para gerenciar CSRF token
 * Garante que o token seja sempre obtido corretamente
 */

// Cache do token CSRF
let cachedToken = null;

// Função SÍNCRONA para obter o CSRF token do meta tag (sempre tenta primeiro)
export const getCsrfTokenFromMeta = () => {
    try {
        // Tenta múltiplas formas de encontrar o meta tag
        let metaTag = null;
        
        // Primeiro tenta do head
        if (document.head) {
            metaTag = document.head.querySelector('meta[name="csrf-token"]');
        }
        
        // Se não encontrar, tenta do document inteiro
        if (!metaTag) {
            metaTag = document.querySelector('meta[name="csrf-token"]');
        }
        
        if (metaTag && metaTag.content && metaTag.content.trim()) {
            const token = metaTag.content.trim();
            cachedToken = token;
            return token;
        }
    } catch (error) {
        console.error('Erro ao ler CSRF token do meta tag:', error);
    }
    return null;
};

// Função para obter CSRF token da API usando fetch (sem axios para evitar loop)
export const getCsrfTokenFromAPI = async () => {
    try {
        const response = await fetch(window.location.origin + '/api/csrf-token', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data?.token && data.token.trim()) {
                const token = data.token.trim();
                cachedToken = token;
                return token;
            }
        }
    } catch (error) {
        console.error('Erro ao buscar CSRF token da API:', error);
    }
    return null;
};

// Função principal para obter CSRF token (sempre retorna uma promise)
export const getCsrfToken = async () => {
    // 1. SEMPRE tenta do meta tag primeiro (síncrono e mais rápido)
    let token = getCsrfTokenFromMeta();
    
    // 2. Se encontrar no meta tag, retorna imediatamente
    if (token) {
        return token;
    }
    
    // 3. Se não encontrar e o DOM não estiver pronto, espera
    if (document.readyState !== 'complete' && document.readyState !== 'interactive') {
        await new Promise(resolve => {
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                resolve();
            } else {
                const onReady = () => {
                    resolve();
                    document.removeEventListener('DOMContentLoaded', onReady);
                    window.removeEventListener('load', onReady);
                };
                document.addEventListener('DOMContentLoaded', onReady);
                window.addEventListener('load', onReady);
            }
        });
        
        // Tenta novamente do meta tag após DOM carregar
        token = getCsrfTokenFromMeta();
        if (token) {
            return token;
        }
    }
    
    // 4. Se ainda não encontrar, busca da API (fallback)
    token = await getCsrfTokenFromAPI();
    if (token) {
        return token;
    }
    
    // 5. Última tentativa: usa cache se existir
    if (cachedToken) {
        console.warn('Usando token CSRF em cache (pode estar expirado)');
        return cachedToken;
    }
    
    // 6. Se nada funcionar, retorna null (o interceptor tratará)
    console.error('Não foi possível obter CSRF token de nenhuma fonte!');
    return null;
};

// Inicializar token quando o módulo é carregado
export const initializeCsrfToken = async () => {
    // Tenta buscar token imediatamente
    const token = await getCsrfToken();
    if (token) {
        cachedToken = token;
    }
    return token;
};

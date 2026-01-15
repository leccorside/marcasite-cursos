import axios from 'axios';
import { getCsrfToken, getCsrfTokenFromMeta, getCsrfTokenFromAPI } from './csrf';

// Variável para cache local
let cachedToken = null;

// Configurar axios para incluir CSRF token e credenciais
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;

// Configurar base URL baseada no ambiente
axios.defaults.baseURL = window.location.origin;

// Cache do último token obtido da API
let lastApiToken = null;
let lastTokenTime = 0;
const TOKEN_CACHE_TIME = 300000; // 5 minutos (tokens CSRF duram muito tempo)

// Interceptor para adicionar CSRF token
axios.interceptors.request.use(async (config) => {
    // Não precisa de CSRF token para GET requests
    const method = config.method?.toLowerCase();
    if (method === 'get' || config.url?.includes('/csrf-token')) {
        return config;
    }

    // Estratégia: Tenta meta tag primeiro (rápido), depois API se necessário
    let token = getCsrfTokenFromMeta();
    
    // Se não encontrar no meta tag ou cache expirou, busca da API
    const now = Date.now();
    if (!token || (lastTokenTime && (now - lastTokenTime) > TOKEN_CACHE_TIME)) {
        // Busca novo token da API (sincronizado com sessão atual)
        const apiToken = await getCsrfTokenFromAPI();
        if (apiToken) {
            token = apiToken;
            lastApiToken = apiToken;
            lastTokenTime = now;
            
            // Atualiza meta tag com token sincronizado
            const metaTag = document.head?.querySelector('meta[name="csrf-token"]') || 
                           document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.content = token;
            }
        }
    }

    // Se ainda não encontrar, rejeita a requisição (evita erro 419)
    if (!token) {
        return Promise.reject(new Error('CSRF token não disponível. Recarregue a página.'));
    }

    // Adiciona token ao header
    config.headers['X-CSRF-TOKEN'] = token;
    config.headers['X-Requested-With'] = 'XMLHttpRequest';

    return config;
}, (error) => {
    return Promise.reject(error);
});

// Interceptor de resposta para atualizar token se necessário (após 419)
axios.interceptors.response.use(
    (response) => {
        // Retorna resposta normalmente
        return response;
    },
    async (error) => {
        // Se erro 419 (CSRF token inválido), tenta atualizar token e refazer requisição
        if (error.response?.status === 419 && error.config && !error.config.__isRetry) {
            // Limpa cache
            lastApiToken = null;
            lastTokenTime = 0;
            
            // Busca novo token da API
            const newToken = await getCsrfTokenFromAPI();
            
            if (newToken) {
                // Atualiza cache
                lastApiToken = newToken;
                lastTokenTime = Date.now();
                
                // Atualiza meta tag
                const metaTag = document.head?.querySelector('meta[name="csrf-token"]') || 
                               document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.content = newToken;
                }
                
                // Marca como retry para evitar loop
                error.config.__isRetry = true;
                error.config.headers['X-CSRF-TOKEN'] = newToken;
                
                // Tenta novamente a requisição (silenciosamente)
                try {
                    return await axios.request(error.config);
                } catch (retryError) {
                    // Se ainda falhar após retry, rejeita
                    return Promise.reject(retryError);
                }
            }
        }
        return Promise.reject(error);
    }
);

// Função auxiliar para atualizar o token CSRF localmente
const updateLocalCsrfToken = (newToken) => {
    if (!newToken) return;
    
    lastApiToken = newToken;
    lastTokenTime = Date.now();
    
    const metaTag = document.head?.querySelector('meta[name="csrf-token"]') || 
                   document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
        metaTag.content = newToken;
    }
};

export const authService = {
    /**
     * Login
     */
    async login(email, password) {
        try {
            const response = await axios.post('/api/login', {
                email,
                password,
            });
            
            // Atualiza token recebido na resposta
            if (response.data?.csrf_token) {
                updateLocalCsrfToken(response.data.csrf_token);
            }

            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                errors: error.response?.data?.errors || {},
                message: error.response?.data?.message || 'Erro ao fazer login',
            };
        }
    },

    /**
     * Registro
     */
    async register(data) {
        try {
            const response = await axios.post('/api/register', data);
            
            // Atualiza token recebido na resposta
            if (response.data?.csrf_token) {
                updateLocalCsrfToken(response.data.csrf_token);
            }

            return {
                success: true,
                data: response.data,
            };
        } catch (error) {
            return {
                success: false,
                errors: error.response?.data?.errors || {},
                message: error.response?.data?.message || 'Erro ao fazer cadastro',
            };
        }
    },

    /**
     * Logout
     */
    async logout() {
        try {
            const response = await axios.post('/api/logout');
            
            // Atualiza token recebido na resposta
            if (response.data?.csrf_token) {
                updateLocalCsrfToken(response.data.csrf_token);
            }

            return {
                success: true,
            };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Erro ao fazer logout',
            };
        }
    },

    /**
     * Verificar usuário autenticado
     */
    async checkAuth() {
        try {
            const response = await axios.get('/api/check-auth');
            return {
                success: true,
                user: response.data.user,
            };
        } catch (error) {
            return {
                success: false,
                user: null,
            };
        }
    },

    /**
     * Recuperar senha
     */
    async forgotPassword(email) {
        try {
            const response = await axios.post('/api/forgot-password', { email });
            return {
                success: true,
                message: response.data.message,
            };
        } catch (error) {
            return {
                success: false,
                errors: error.response?.data?.errors || {},
                message: error.response?.data?.message || 'Erro ao enviar email de recuperação',
            };
        }
    },

    /**
     * Resetar senha
     */
    async resetPassword(token, email, password, passwordConfirmation) {
        try {
            const response = await axios.post('/api/reset-password', {
                token,
                email,
                password,
                password_confirmation: passwordConfirmation,
            });
            return {
                success: true,
                message: response.data.message,
            };
        } catch (error) {
            return {
                success: false,
                errors: error.response?.data?.errors || {},
                message: error.response?.data?.message || 'Erro ao resetar senha',
            };
        }
    },
};

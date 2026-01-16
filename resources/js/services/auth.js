import axios from 'axios';

// Configurar base URL baseada no ambiente
axios.defaults.baseURL = window.location.origin;

// Função para obter token do localStorage
const getToken = () => {
    return localStorage.getItem('auth_token');
};

// Função para salvar token no localStorage
const setToken = (token) => {
    if (token) {
        localStorage.setItem('auth_token', token);
    } else {
        localStorage.removeItem('auth_token');
    }
};

// Interceptor para adicionar Bearer token
axios.interceptors.request.use(
    (config) => {
        const token = getToken();
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor de resposta para tratar erros de autenticação
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    async (error) => {
        // Se erro 401 (não autorizado), limpar token e redirecionar para login
        if (error.response?.status === 401) {
            setToken(null);
            // Redirecionar para login apenas se não estiver já na página de login
            if (!window.location.pathname.includes('/login')) {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

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
            
            // Salvar token no localStorage
            if (response.data?.token) {
                setToken(response.data.token);
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
            
            // Salvar token no localStorage
            if (response.data?.token) {
                setToken(response.data.token);
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
            await axios.post('/api/logout');
            // Remover token do localStorage
            setToken(null);
            return {
                success: true,
            };
        } catch (error) {
            // Mesmo com erro, remover token localmente
            setToken(null);
            return {
                success: true,
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
            // Se erro 401, limpar token
            if (error.response?.status === 401) {
                setToken(null);
            }
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

    /**
     * Obter token atual
     */
    getToken() {
        return getToken();
    },
};

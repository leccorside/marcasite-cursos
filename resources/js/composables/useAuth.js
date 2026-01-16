import { ref, computed } from 'vue';
import { authService } from '@/services/auth';

// Estado global compartilhado
const user = ref(null);
const loading = ref(false);
const lastCheckTime = ref(0);
const CHECK_CACHE_TIME = 30000; // 30 segundos de cache
let checkingPromise = null; // Evitar múltiplas chamadas simultâneas

export function useAuth() {
    /**
     * Verificar autenticação (com cache e proteção contra chamadas duplicadas)
     */
    const checkAuth = async (force = false) => {
        // Se já está verificando, retorna a promise existente
        if (checkingPromise && !force) {
            return checkingPromise;
        }

        // Se tem cache recente e não é forçado, retorna sem fazer requisição
        const now = Date.now();
        if (!force && user.value && (now - lastCheckTime.value) < CHECK_CACHE_TIME) {
            return;
        }

        // Cria promise única para evitar requisições duplicadas
        checkingPromise = (async () => {
            loading.value = true;
            try {
                const result = await authService.checkAuth();
                if (result.success && result.user) {
                    user.value = result.user;
                } else {
                    user.value = null;
                }
                lastCheckTime.value = now;
            } catch (error) {
                user.value = null;
            } finally {
                loading.value = false;
                checkingPromise = null; // Limpa a promise
            }
        })();

        return checkingPromise;
    };

    /**
     * Login
     */
    const login = async (email, password) => {
        loading.value = true;
        try {
            const result = await authService.login(email, password);
            if (result.success) {
                user.value = result.data.user;
                lastCheckTime.value = Date.now(); // Atualiza cache após login
                return { success: true, redirect: result.data.redirect || '/' };
            }
            return result;
        } catch (error) {
            return {
                success: false,
                message: 'Erro ao fazer login',
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Registro
     */
    const register = async (data) => {
        loading.value = true;
        try {
            const result = await authService.register(data);
            if (result.success) {
                user.value = result.data.user;
                lastCheckTime.value = Date.now(); // Atualiza cache após registro
                return { success: true, redirect: result.data.redirect || '/' };
            }
            return result;
        } catch (error) {
            return {
                success: false,
                message: 'Erro ao fazer cadastro',
            };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Logout
     */
    const logout = async () => {
        loading.value = true;
        try {
            await authService.logout();
            user.value = null;
            lastCheckTime.value = 0; // Limpa cache após logout
            checkingPromise = null; // Limpa promise pendente
            return { success: true };
        } catch (error) {
            // Mesmo com erro, limpar estado local
            user.value = null;
            lastCheckTime.value = 0;
            checkingPromise = null;
            return { success: true };
        } finally {
            loading.value = false;
        }
    };

    /**
     * Recuperar senha
     */
    const forgotPassword = async (email) => {
        loading.value = true;
        try {
            const result = await authService.forgotPassword(email);
            return result;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Resetar senha
     */
    const resetPassword = async (token, email, password, passwordConfirmation) => {
        loading.value = true;
        try {
            const result = await authService.resetPassword(token, email, password, passwordConfirmation);
            return result;
        } finally {
            loading.value = false;
        }
    };

    const isAuthenticated = computed(() => !!user.value);
    const isAdmin = computed(() => user.value?.tipo === 'admin');
    const isAluno = computed(() => user.value?.tipo === 'aluno');

    return {
        user,
        loading,
        isAuthenticated,
        isAdmin,
        isAluno,
        checkAuth,
        login,
        register,
        logout,
        forgotPassword,
        resetPassword,
    };
}

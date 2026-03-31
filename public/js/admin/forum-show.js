// ===== ADMIN FORUM SHOW JAVASCRIPT (Vue 3) =====
// This file contains Vue.js application for the admin single post view

const { createApp, ref, reactive } = Vue;

createApp({
    setup() {
        // Reactive data
        const post = reactive({
            id: window.postData.id,
            is_pinned: window.postData.is_pinned,
            is_announcement: window.postData.is_announcement,
            user: window.postData.user
        });
        
        const flags = ref(window.flagsData.map(flag => ({
            ...flag,
            reporter_name: flag.reporter?.name || 'Unknown',
            created_at_diff: flag.created_at_diff || new Date(flag.created_at).toLocaleDateString()
        })));
        
        const successMessage = ref('');
        const errorMessage = ref('');
        const restrictionDays = ref(7);
        
        // Helper functions
        const showSuccess = (message) => {
            successMessage.value = message;
            setTimeout(() => { successMessage.value = ''; }, 3000);
        };
        
        const showError = (message) => {
            errorMessage.value = message;
            setTimeout(() => { errorMessage.value = ''; }, 3000);
        };
        
        const formatDate = (dateString) => {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString();
        };
        
        // Toggle pin
        const togglePin = async () => {
            try {
                const url = window.togglePinUrl.replace('__POST_ID__', post.id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    post.is_pinned = data.is_pinned;
                    showSuccess(data.message || 'Pin status updated');
                } else {
                    showError(data.message || 'Failed to update pin status');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred');
            }
        };
        
        // Toggle announcement
        const toggleAnnouncement = async () => {
            try {
                const url = window.toggleAnnouncementUrl.replace('__POST_ID__', post.id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    post.is_announcement = data.is_announcement;
                    showSuccess(data.message || 'Announcement status updated');
                } else {
                    showError(data.message || 'Failed to update announcement status');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred');
            }
        };
        
        // Delete reply
        const deleteReply = async (replyId) => {
            if (!confirm('Are you sure you want to delete this reply?')) return;
            
            try {
                const url = window.deleteReplyUrl.replace('__REPLY_ID__', replyId);
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    // Remove reply from DOM
                    const replyElement = document.getElementById(`reply-${replyId}`);
                    if (replyElement) replyElement.remove();
                    showSuccess('Reply deleted successfully');
                    // Reload to update counts
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showError(data.message || 'Failed to delete reply');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred');
            }
        };
        
        // Update flag status
        const updateFlag = async (flagId, status) => {
            try {
                const url = window.updateFlagUrl.replace('__FLAG_ID__', flagId);
                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });
                const data = await response.json();
                if (data.success) {
                    // Update flag in local array
                    const index = flags.value.findIndex(f => f.id === flagId);
                    if (index !== -1) {
                        flags.value[index].status = status;
                    }
                    showSuccess(`Flag marked as ${status}`);
                } else {
                    showError(data.message || 'Failed to update flag');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred');
            }
        };
        
        // Restrict user
        const restrictUser = async (userId) => {
            const days = restrictionDays.value;
            if (!days || days < 1 || days > 365) {
                showError('Please enter a valid number of days (1-365)');
                return;
            }
            
            try {
                const url = window.restrictUserUrl.replace('__USER_ID__', userId);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ days })
                });
                const data = await response.json();
                if (data.success) {
                    if (post.user) {
                        post.user.forum_restricted_until = data.restricted_until;
                    }
                    showSuccess(`User restricted for ${days} days`);
                } else {
                    showError(data.message || 'Failed to restrict user');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred');
            }
        };
        
        // Unrestrict user
        const unrestrictUser = async (userId) => {
            try {
                const url = window.unrestrictUserUrl.replace('__USER_ID__', userId);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    if (post.user) {
                        post.user.forum_restricted_until = null;
                    }
                    showSuccess('User restriction removed');
                } else {
                    showError(data.message || 'Failed to remove restriction');
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred');
            }
        };
        
        // Confirm delete
        const confirmDelete = () => {
            const modal = document.getElementById('deleteModal');
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = window.deletePostUrl.replace('__POST_ID__', post.id);
            modal.style.display = 'flex';
        };
        
        // Modal close handlers
        const setupModal = () => {
            const modal = document.getElementById('deleteModal');
            const cancelBtn = document.getElementById('cancelDelete');
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            }
            
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        };
        
        // Initialize
        setupModal();
        
        return {
            post,
            flags,
            successMessage,
            errorMessage,
            restrictionDays,
            formatDate,
            togglePin,
            toggleAnnouncement,
            deleteReply,
            updateFlag,
            restrictUser,
            unrestrictUser,
            confirmDelete
        };
    }
}).mount('#postApp');
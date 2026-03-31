const { createApp, ref, onMounted } = Vue;

const app = createApp({
    setup() {
        const selectedFlags = ref([]);
        const allSelected = ref(false);
        const showNotesModal = ref(false);
        const currentFlagId = ref(null);
        const notesContent = ref('');
        const filters = ref({
            status: '',
            reporter: '',
            reason: '',
            date_from: '',
            date_to: ''
        });

        const toggleSelection = (flagId) => {
            const index = selectedFlags.value.indexOf(flagId);
            if (index === -1) {
                selectedFlags.value.push(flagId);
            } else {
                selectedFlags.value.splice(index, 1);
            }
            updateAllSelected();
        };

        const isSelected = (flagId) => {
            return selectedFlags.value.includes(flagId);
        };

        const selectAll = (event) => {
            if (event.target.checked) {
                // Select all visible flags
                const checkboxes = document.querySelectorAll('.flags-table tbody input[type="checkbox"]');
                selectedFlags.value = Array.from(checkboxes).map(cb => parseInt(cb.value));
            } else {
                selectedFlags.value = [];
            }
            allSelected.value = event.target.checked;
        };

        const updateAllSelected = () => {
            const totalCheckboxes = document.querySelectorAll('.flags-table tbody input[type="checkbox"]').length;
            allSelected.value = selectedFlags.value.length === totalCheckboxes && totalCheckboxes > 0;
        };

        const clearSelection = () => {
            selectedFlags.value = [];
            allSelected.value = false;
        };

        const bulkResolve = async () => {
            if (selectedFlags.value.length === 0) return;
            
            if (!confirm(`Resolve ${selectedFlags.value.length} selected flag(s)?`)) return;
            
            try {
                const response = await fetch('/admin/flags/bulk-update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        flag_ids: selectedFlags.value,
                        action: 'resolve'
                    })
                });
                
                if (response.ok) {
                    showNotification('Flags resolved successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                showNotification('Error resolving flags', 'error');
            }
        };

        const bulkDismiss = async () => {
            if (selectedFlags.value.length === 0) return;
            
            if (!confirm(`Dismiss ${selectedFlags.value.length} selected flag(s)?`)) return;
            
            try {
                const response = await fetch('/admin/flags/bulk-update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        flag_ids: selectedFlags.value,
                        action: 'dismiss'
                    })
                });
                
                if (response.ok) {
                    showNotification('Flags dismissed successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                showNotification('Error dismissing flags', 'error');
            }
        };

        const updateFlag = async (flagId, status) => {
            if (!confirm(`Mark this flag as ${status}?`)) return;
            
            try {
                const response = await fetch(`/admin/flags/${flagId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });
                
                if (response.ok) {
                    showNotification(`Flag ${status} successfully!`, 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                showNotification('Error updating flag', 'error');
            }
        };

        const showNotes = (flagId) => {
            currentFlagId.value = flagId;
            notesContent.value = '';
            showNotesModal.value = true;
        };

        const closeNotesModal = () => {
            showNotesModal.value = false;
            currentFlagId.value = null;
            notesContent.value = '';
        };

        const saveNotes = async () => {
            if (!currentFlagId.value) return;
            
            try {
                const response = await fetch(`/admin/flags/${currentFlagId.value}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        moderation_notes: notesContent.value
                    })
                });
                
                if (response.ok) {
                    showNotification('Notes saved successfully!', 'success');
                    closeNotesModal();
                    setTimeout(() => location.reload(), 1500);
                }
            } catch (error) {
                showNotification('Error saving notes', 'error');
            }
        };

        const refreshData = () => {
            location.reload();
        };

        const applyFilters = (event) => {
            // Form will submit naturally
            return true;
        };

        const showNotification = (message, type) => {
            // You can implement a toast notification here
            alert(message);
        };

        return {
            selectedFlags,
            allSelected,
            showNotesModal,
            notesContent,
            filters,
            toggleSelection,
            isSelected,
            selectAll,
            clearSelection,
            bulkResolve,
            bulkDismiss,
            updateFlag,
            showNotes,
            closeNotesModal,
            saveNotes,
            refreshData,
            applyFilters
        };
    }
});

app.mount('#flagsApp');
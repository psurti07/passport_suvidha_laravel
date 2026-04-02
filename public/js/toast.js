function showToast(message, type = 'success') {

    let bgColorClass, borderColorClass, iconSvg;

    switch (type) {

        case 'success':
            bgColorClass = 'bg-emerald-500/95';
            borderColorClass = 'border-emerald-400';
            iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`;
            break;

        case 'error':
            bgColorClass = 'bg-red-500/95';
            borderColorClass = 'border-red-400';
            iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`;
            break;

        case 'warning':
            bgColorClass = 'bg-amber-500/95';
            borderColorClass = 'border-amber-400';
            iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-4a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>`;
            break;

        default:
            bgColorClass = 'bg-blue-500/95';
            borderColorClass = 'border-blue-400';
            iconSvg = `<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>`;
    }

    const toastNode = document.createElement('div');

    toastNode.className = `
        toast-content flex items-center w-full max-w-sm p-4 mb-4 
        ${bgColorClass} text-white rounded-lg shadow-2xl border ${borderColorClass}
    `;

    toastNode.innerHTML = `
        <div class="text-lg">${iconSvg}</div>
        <div class="ml-3 text-sm font-medium">${message}</div>
        <button class="ml-3 text-white hover:text-gray-200">✕</button>
    `;

    toastNode.querySelector('button').onclick = () => {
        toastNode.parentElement.remove();
    };

    Toastify({
        node: toastNode,
        duration: 4000,
        gravity: "top",
        position: "right",
        stopOnFocus: true,
    }).showToast();
}

function toast(message, type = 'success') {
    showToast(message, type);
}
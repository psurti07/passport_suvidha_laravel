function confirmDelete(message, form) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to able to delete this "${message}".`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-800',
            confirmButton: 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 mx-1',
            cancelButton: 'px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 mx-1'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
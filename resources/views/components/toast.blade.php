@push('scripts')
<script>
    @if(session('success'))
        toast(@json(session('success')), 'success');
    @endif

    @if(session('error'))
        toast(@json(session('error')), 'error');
    @endif

    @if(session('warning'))
        toast(@json(session('warning')), 'warning');
    @endif
</script>
@endpush
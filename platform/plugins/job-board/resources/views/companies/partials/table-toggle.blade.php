<x-core::form :url="$url" method="POST">
    @csrf
    @if (! empty($inputName))
        <x-core::form.toggle
            :name="$inputName"
            :checked="$checked"
            :label="$label ?? null"
            onchange="this.form.submit()"
            wrapperClass="mb-0"
        />
    @endif
</x-core::form>

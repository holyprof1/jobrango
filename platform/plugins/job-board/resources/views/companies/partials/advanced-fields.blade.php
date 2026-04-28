<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="tax_id">{{ trans('plugins/job-board::forms.tax_id') }}</label>
        <input
            class="form-control"
            id="tax_id"
            name="tax_id"
            type="text"
            value="{{ old('tax_id', $company->tax_id) }}"
            maxlength="60"
        >
    </div>
    <div class="col-12">
        <label class="form-label" for="unique_id">{{ trans('plugins/job-board::job-board.form.unique_id') }}</label>
        <input
            class="form-control"
            id="unique_id"
            name="unique_id"
            type="text"
            value="{{ old('unique_id', $company->getKey() ? $company->unique_id : $company->generateUniqueId()) }}"
        >
        <small class="form-hint">{{ __('This internal ID is mainly for admin lookup and imports.') }}</small>
    </div>
</div>

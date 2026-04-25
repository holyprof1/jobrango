@if (is_plugin_active('newsletter'))
    <section class="section-box mt-50 mb-20">
        <div class="container">
            <div class="box-newsletter jobrango-newsletter-clean">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-7 col-12">
                        <h2 class="text-md-newsletter text-center">
                            {!! BaseHelper::clean($config['title']) !!}
                        </h2>

                        <div class="box-form-newsletter mt-40">
                            {!! $form->renderForm() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

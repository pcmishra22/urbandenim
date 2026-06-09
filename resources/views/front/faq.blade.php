@extends('layouts.eshopper')
@section('title', 'FAQs - Jeanzo')
@section('content')

@include('front.partials.page-banner', ['title' => 'FAQs', 'breadcrumb' => 'FAQs'])

<div class="container-fluid pt-5 pb-5">
    <div class="row px-xl-5">
        <div class="col-lg-10 offset-lg-1">
            <div class="text-center mb-5">
                <h2 class="section-title px-5"><span class="px-2">Frequently Asked Questions</span></h2>
                <p class="text-muted">Can't find an answer? <a href="{{ route('contact') }}">Contact our support team</a>.</p>
            </div>

            @if($faqs->isEmpty())
                <div class="text-center py-5 bg-light rounded">
                    <i class="fa fa-question-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No FAQs available yet. Check back soon!</p>
                </div>
            @else
                @php $categories = $faqs->groupBy('category'); @endphp
                @foreach($categories as $category => $items)
                    @if($category)
                        <h5 class="font-weight-bold mt-5 mb-3 border-bottom pb-2">{{ $category }}</h5>
                    @endif
                    <div class="accordion" id="faqAccordion{{ $loop->index }}">
                        @foreach($items as $faq)
                        <div class="card border-0 border-bottom mb-2">
                            <div class="card-header bg-transparent border-0 p-0">
                                <h6 class="mb-0">
                                    <button class="btn btn-link text-dark font-weight-semi-bold text-left w-100 py-3 px-0"
                                            type="button" data-toggle="collapse"
                                            data-target="#faq{{ $faq->id }}"
                                            aria-expanded="{{ $loop->first && $loop->parent->first ? 'true' : 'false' }}">
                                        <i class="fa fa-question-circle text-primary mr-2"></i>{{ $faq->question }}
                                        <i class="fa fa-chevron-down float-right mt-1 small"></i>
                                    </button>
                                </h6>
                            </div>
                            <div id="faq{{ $faq->id }}" class="collapse {{ $loop->first && $loop->parent->first ? 'show' : '' }}"
                                 data-parent="#faqAccordion{{ $loop->parent->index }}">
                                <div class="card-body pt-0 pb-3 px-0 text-muted">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endforeach
            @endif

            <div class="bg-light rounded p-5 mt-5 text-center">
                <h5 class="font-weight-bold mb-2">Still have questions?</h5>
                <p class="text-muted mb-3">Our support team is here to help you 24/7.</p>
                <a href="{{ route('contact') }}" class="btn btn-primary px-4">Contact Support</a>
            </div>
        </div>
    </div>
</div>
@endsection

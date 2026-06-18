@extends('layouts.eshopper')
@section('title', 'Frequently Asked Questions — Jeanzo')
@section('meta_description', 'Got questions about Jeanzo? Find answers about shipping, returns, sizing, payment methods, exchanges and more in our FAQ page.')
@section('canonical', route('faq'))
@section('og_title', 'FAQs — Jeanzo Denim')
@section('og_description', 'Answers to common questions about orders, shipping, returns, sizing and payment at Jeanzo.')
@section('content')

<div class="container-fluid px-xl-5 pt-4 pb-5" style="background:#faf8f8;">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="font-weight-bold mb-1" style="color:#2d2d2d;">
                        <i class="fa fa-question-circle mr-2" style="color:#D19C97;"></i>Frequently Asked Questions
                    </h3>
                    <div style="font-size:.85rem;">
                        <a href="{{ url('/') }}" class="text-muted" style="text-decoration:none;">Home</a>
                        <span class="text-muted mx-1">›</span>
                        <span style="color:#D19C97;">FAQs</span>
                    </div>
                </div>
            </div>
            <div style="width:50px;height:3px;background:#D19C97;border-radius:2px;margin-top:10px;"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <p class="text-muted mb-4">Can't find an answer? <a href="{{ route('contact') }}" style="color:#D19C97;">Contact our support team</a>.</p>

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

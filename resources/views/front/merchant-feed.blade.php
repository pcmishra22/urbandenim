<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">
<channel>
    <title>Jeanzo Product Feed</title>
    <link>{{ url('/') }}</link>
    <description>Jeanzo denim &amp; apparel product feed for Google Merchant Center</description>

    @foreach($products as $product)
    @php
        // Stock + price: prefer variants if the product has any, else the base product fields
        $variants   = $product->variants;
        $totalStock = $variants->count() ? $variants->sum('quantity') : ($product->quantity ?? 0);
        $inStock    = $totalStock > 0;

        $price      = $variants->count() ? $variants->min('price') : $product->price;
        $salePrice  = $product->sale_price && $product->sale_price < $price ? $product->sale_price : null;

        $img        = $product->images->first();
        $imgUrl     = $img
            ? asset('storage/products/' . $product->id . '/images/' . ($img->image ?? ''))
            : asset('storage/default.jpeg');

        $extraImages = $product->images->skip(1)->take(10);

        // Map internal gender values to Google's accepted enum
        $genderMap = [
            'men' => 'male', 'man' => 'male', 'boys' => 'male', 'boy' => 'male',
            'women' => 'female', 'woman' => 'female', 'girls' => 'female', 'girl' => 'female',
        ];
        $gGender   = $genderMap[strtolower($product->gender ?? '')] ?? 'unisex';

        $ageMap    = ['boys' => 'kids', 'boy' => 'kids', 'girls' => 'kids', 'girl' => 'kids'];
        $gAgeGroup = $ageMap[strtolower($product->gender ?? '')] ?? 'adult';
    @endphp
    <item>
        <g:id>{{ $product->sku ?? $product->id }}</g:id>
        <title><![CDATA[{{ $product->name }}]]></title>
        <description><![CDATA[{{ strip_tags($product->short_description ?? $product->description ?? $product->name) }}]]></description>
        <link>{{ route('products.detail', $product->slug) }}</link>
        <g:image_link>{{ $imgUrl }}</g:image_link>
        @foreach($extraImages as $extra)
        <g:additional_image_link>{{ asset('storage/products/' . $product->id . '/images/' . ($extra->image ?? '')) }}</g:additional_image_link>
        @endforeach
        <g:availability>{{ $inStock ? 'in stock' : 'out of stock' }}</g:availability>
        <g:price>{{ number_format($price, 2, '.', '') }} INR</g:price>
        @if($salePrice)
        <g:sale_price>{{ number_format($salePrice, 2, '.', '') }} INR</g:sale_price>
        @endif
        <g:condition>new</g:condition>
        <g:brand>{{ $product->brand->name ?? 'Jeanzo' }}</g:brand>
        <g:product_type><![CDATA[{{ $product->category->name ?? 'Apparel' }}]]></g:product_type>
        <g:google_product_category>Apparel &amp; Accessories &gt; Clothing &gt; Pants &gt; Jeans</g:google_product_category>
        <g:gender>{{ $gGender }}</g:gender>
        <g:age_group>{{ $gAgeGroup }}</g:age_group>
        @if($product->color_family)
        <g:color>{{ $product->color_family }}</g:color>
        @endif
        <g:identifier_exists>false</g:identifier_exists>
        <g:shipping>
            <g:country>IN</g:country>
            <g:service>Standard</g:service>
            <g:price>0.00 INR</g:price>
        </g:shipping>
    </item>
    @endforeach

</channel>
</rss>

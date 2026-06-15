<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // ── CATEGORIES ────────────────────────────────────────────────
        $categories = [
            ['name' => 'Style Guide',     'description' => 'Expert tips on how to wear, style and pair your jeans for every occasion.'],
            ['name' => 'Fit & Comfort',   'description' => 'Find your perfect fit — from skinny to straight, slim to relaxed.'],
            ['name' => 'Care & Maintain', 'description' => 'Keep your denim looking fresh longer with our care guides.'],
            ['name' => 'Trends',          'description' => 'What\'s hot in denim right now — season by season.'],
            ['name' => 'Brand Story',     'description' => 'Behind the seams — the Jeenzo story, values and craftsmanship.'],
        ];

        $cats = [];
        foreach ($categories as $cat) {
            $cats[$cat['name']] = BlogCategory::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                array_merge($cat, ['is_active' => true])
            );
        }

        // ── TAGS ──────────────────────────────────────────────────────
        $tagNames = [
            'denim', 'jeans', 'style tips', 'men\'s fashion', 'women\'s fashion',
            'skinny jeans', 'slim fit', 'straight leg', 'relaxed fit', 'bootcut',
            'denim care', 'outfit ideas', 'casual wear', 'office look', 'festive fashion',
            'summer denim', 'monsoon style', 'winter denim', 'street style', 'sustainable fashion',
        ];

        $tags = [];
        foreach ($tagNames as $tagName) {
            $tags[$tagName] = BlogTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName, 'is_active' => true]
            );
        }

        // ── POSTS ─────────────────────────────────────────────────────
        $posts = [

            // ── Style Guide ──────────────────────────────────────────

            [
                'title'       => 'How to Style Slim Fit Jeans for Every Occasion',
                'category'    => 'Style Guide',
                'excerpt'     => 'Slim fit jeans are the most versatile piece in your wardrobe. Here\'s how to wear them from Monday morning meetings to Saturday night dinners.',
                'content'     => '<p>If there is one pair of jeans every man and woman in India needs to own right now, it is a well-fitted pair of slim fit jeans. At Jeenzo, our slim fit range is built for the Indian body — a slightly tapered leg, mid-rise waist, and just enough stretch to keep you comfortable from a 9 AM office commute to a 10 PM dinner.</p>

<h3>The Office Look</h3>
<p>Pair your Jeenzo slim fit in dark indigo with a crisp white Oxford shirt tucked loosely at the front. Add tan derby shoes and a leather belt in the same shade. You look polished without trying too hard. Avoid heavily distressed washes for formal settings — save those for weekends.</p>

<h3>The Weekend Casual</h3>
<p>A mid-wash slim fit paired with a plain round-neck tee and white sneakers is a foolproof weekend look. Roll up the hem by one cuff for a cleaner silhouette, especially if you are on the shorter side. This works equally well for men and women.</p>

<h3>The Evening Out</h3>
<p>Go for a black slim fit. Black denim elevates naturally — add a slim blazer, a fitted polo, or for women, a sheer top tucked in. Block-heel ankle boots finish the look.</p>

<h3>The Festival Look</h3>
<p>India has no shortage of weddings, pujas, and family functions. A light-wash or ice-blue slim fit paired with a kurta or a structured ethnic top works beautifully for semi-formal festive occasions — comfortable enough to sit through a three-hour ceremony, stylish enough for the photos.</p>

<p>The key to making slim fit jeans work across all occasions is the wash and what sits on top. Jeenzo offers 14 washes in our slim fit range — from raw indigo to acid wash — so you have a pair for every moment.</p>',
                'tags'        => ['slim fit', 'style tips', 'men\'s fashion', 'women\'s fashion', 'office look', 'casual wear'],
                'is_featured' => true,
                'published_at'=> now()->subDays(2),
                'meta_title'  => 'How to Style Slim Fit Jeans for Every Occasion | Jeenzo',
                'meta_desc'   => 'Style slim fit jeans for office, weekends, evenings and festive occasions. Expert tips from Jeenzo — India\'s premium denim brand.',
            ],

            [
                'title'       => 'The Ultimate Guide to Women\'s Jeans Fits — Find Your Perfect Pair',
                'category'    => 'Style Guide',
                'excerpt'     => 'Skinny, straight, relaxed, bootcut — so many options. We break down every women\'s jeans fit so you know exactly what to pick for your body and lifestyle.',
                'content'     => '<p>Walking into a jeans store — or browsing online — can feel overwhelming. Skinny, straight, slim, relaxed, boyfriend, bootcut, wide leg. What does it all actually mean and which one is right for you? At Jeenzo, we believe every woman deserves to know exactly what she is buying. So here is your no-nonsense guide.</p>

<h3>Skinny Fit</h3>
<p>Sits close to the leg from hip to ankle. The most form-fitting option. Ideal for pairing with oversized tops, long kurtas, or tucked-in shirts. Works for most body types — petite women find it lengthening, curvy women find it flattering when the rise is high enough. Jeenzo\'s skinny fit uses a 2% elastane blend for all-day comfort.</p>

<h3>Slim Fit</h3>
<p>Slightly roomier than skinny — tapered from the thigh down but not skin-tight. A great everyday option. Goes well with fitted tops and blazers. This is the safest first pair of jeans for someone new to denim.</p>

<h3>Straight Leg</h3>
<p>Same width from hip to hem — a clean, classic silhouette. Universally flattering. Currently the most popular cut in India across age groups. Pairs beautifully with heels to elongate the leg or with sneakers for casual ease.</p>

<h3>Relaxed Fit</h3>
<p>More room through the hip and thigh, tapering slightly at the ankle. Ideal for comfort-first dressing — long travel days, working from home, running errands. Does not mean shapeless — the right relaxed fit still looks intentional and put-together.</p>

<h3>Bootcut</h3>
<p>Fitted through the thigh and flares slightly from the knee down — originally designed to fit over boots. Still a favourite because the flare balances wider hips beautifully and makes legs look longer. Works with heels and with Western boots.</p>

<h3>Wide Leg</h3>
<p>The style moment of the last two seasons in India. Dramatic and effortless. Tuck in a fitted top and add block heels — done. Works best for taller frames or women who want to create the illusion of height.</p>

<p>The best approach: own two different fits. One slim or skinny for dressed-up days, one straight or relaxed for everyday ease. Between those two, you have 90% of your denim needs covered.</p>',
                'tags'        => ['women\'s fashion', 'skinny jeans', 'straight leg', 'relaxed fit', 'bootcut', 'style tips', 'outfit ideas'],
                'is_featured' => true,
                'published_at'=> now()->subDays(5),
                'meta_title'  => 'Women\'s Jeans Fits Guide — Skinny, Straight, Relaxed & More | Jeenzo',
                'meta_desc'   => 'Not sure which women\'s jeans fit to buy? Jeenzo breaks down every fit — skinny, slim, straight, relaxed, bootcut, wide leg — so you find your perfect pair.',
            ],

            [
                'title'       => '5 Ways to Wear Straight Leg Jeans This Season',
                'category'    => 'Style Guide',
                'excerpt'     => 'Straight leg jeans are back at the top — and they are not going anywhere. Here are five fresh outfit ideas to get the most out of your pair.',
                'content'     => '<p>If the last decade belonged to the skinny jean, the current moment belongs to the straight leg. Clean, balanced, and universally flattering — straight leg jeans from Jeenzo are our single best-selling cut for good reason. But how do you keep the look fresh? Here are five ways to wear them right now.</p>

<h3>1. The Tucked-In Tee</h3>
<p>Take a plain white or black tee, tuck it fully into high-waist straight leg jeans, and add a thin leather belt. Simple, modern, and extremely wearable for day outings, college, or casual office environments.</p>

<h3>2. The Blazer Over Tee</h3>
<p>A slightly oversized blazer over a basic tee with straight leg jeans is the formula for effortless cool. This combination works in light linen blazers for summer and heavier woolen ones through December and January.</p>

<h3>3. The Kurta-Jean Combo</h3>
<p>India\'s own style invention — a short or mid-length kurta over straight leg jeans. This is the outfit of a million college students and it works because it does. Choose a straight leg that sits right at the ankle to let the kurta hem breathe.</p>

<h3>4. The Knotted Shirt</h3>
<p>An oversized button-down shirt knotted at the front — or half-tucked — over a mid-wash straight leg jean reads casual but considered. Add slides or block-heel mules.</p>

<h3>5. The Monochrome Moment</h3>
<p>All-denim — a lighter denim shirt or jacket over your straight leg jeans. Match washes for a sleek look or contrast light and dark for the classic Canadian tuxedo Indian twist.</p>

<p>Straight leg jeans are available in 12 washes at Jeenzo, from raw midnight blue to washed grey. <a href="/products?category=2">Shop the range here.</a></p>',
                'tags'        => ['straight leg', 'style tips', 'outfit ideas', 'casual wear', 'women\'s fashion', 'street style'],
                'is_featured' => false,
                'published_at'=> now()->subDays(10),
                'meta_title'  => '5 Ways to Wear Straight Leg Jeans | Jeenzo Style Guide',
                'meta_desc'   => 'Fresh outfit ideas for straight leg jeans — tucked tees, blazers, kurtas and more. Style inspiration from Jeenzo.',
            ],

            // ── Fit & Comfort ─────────────────────────────────────────

            [
                'title'       => 'How to Find Your Perfect Jeans Size — The Jeenzo Sizing Guide',
                'category'    => 'Fit & Comfort',
                'excerpt'     => 'Buying jeans online and unsure about sizing? This guide covers waist, hip, inseam, and rise — everything you need to get the right fit the first time.',
                'content'     => '<p>Sizing in denim is more nuanced than standard clothing because a good pair of jeans needs to fit across multiple dimensions — not just your waist. Here is how to measure yourself correctly and translate that into the right Jeenzo size.</p>

<h3>The Four Measurements That Matter</h3>

<h4>1. Waist</h4>
<p>Measure around your natural waist — the narrowest part of your torso, roughly two inches above your belly button. Do not suck in. Write down this number in centimetres.</p>

<h4>2. Hip</h4>
<p>Measure around the fullest part of your hips and seat, usually about 8–9 inches below your natural waist. This is the most important measurement for getting jeans over your hips without gaping at the waist.</p>

<h4>3. Inseam</h4>
<p>Measure from your crotch seam to the bottom of your ankle. The easiest way is to measure a pair of trousers you already love. This tells you the right leg length.</p>

<h4>4. Rise</h4>
<p>Rise is the distance from the crotch seam to the top of the waistband. Low rise sits below the hip bone. Mid rise at or just above. High rise at the natural waist. Most people find mid to high rise most comfortable for everyday wear.</p>

<h3>Jeenzo Sizing</h3>
<p>Our jeans are sized in inches (waist x length). A 32x32 means 32-inch waist, 32-inch inseam. We also offer a size chart in centimetres on every product page. If you are between sizes, size up — denim stretches with wear, especially non-stretch cotton.</p>

<h3>Stretch vs Non-Stretch</h3>
<p>Jeenzo offers both. Stretch denim (with elastane) is more forgiving and comfortable through the day — ideal for curvy fits and active lifestyles. Non-stretch raw denim moulds to your body over time and has a more structured, premium feel.</p>

<p>Still unsure? Our customer support team is available on WhatsApp to help you pick the right size before you order.</p>',
                'tags'        => ['fit & comfort', 'jeans', 'slim fit', 'skinny jeans', 'relaxed fit'],
                'is_featured' => false,
                'published_at'=> now()->subDays(14),
                'meta_title'  => 'Jeans Sizing Guide — How to Find Your Perfect Fit | Jeenzo',
                'meta_desc'   => 'How to measure waist, hip, inseam and rise to buy the perfect jeans online. Complete sizing guide from Jeenzo.',
            ],

            [
                'title'       => 'Skinny vs Slim Fit Jeans — What\'s the Actual Difference?',
                'category'    => 'Fit & Comfort',
                'excerpt'     => 'The words skinny and slim are often used interchangeably — but they are not the same. Here is exactly what separates these two cuts and which one is right for you.',
                'content'     => '<p>Of all the questions our customer team receives, this is one of the most common: what is the difference between skinny and slim fit jeans? The terms do get used loosely across brands, but at Jeenzo we are precise about it. Here is the definitive breakdown.</p>

<h3>Skinny Fit</h3>
<p>Skinny jeans are cut close to the leg throughout — from the hip right down to the ankle. They sit tight across the thigh, knee, and calf. The silhouette is narrow and elongating. Almost all skinny jeans use stretch fabric (elastane blend) because a purely cotton cut this close would be impossible to pull on.</p>
<p><strong>Best for:</strong> Slim to medium builds. Petite frames — makes legs look longer. Pairing with oversized tops and long kurtas where the contrast in volume works beautifully.</p>
<p><strong>Avoid if:</strong> You want more room through the thigh or find tight fabric uncomfortable for long sitting periods.</p>

<h3>Slim Fit</h3>
<p>Slim fit jeans are tapered — fitted through the hip and thigh but with a bit more room than skinny. They narrow from the knee down but are not skin-tight. The ankle opening is smaller than a straight leg but wider than a skinny. Available in both stretch and non-stretch.</p>
<p><strong>Best for:</strong> Nearly every body type. This is the most versatile cut. Works for office and casual both. Looks tailored without being restrictive.</p>
<p><strong>Best for:</strong> Men who want a sharp silhouette without the compression of skinny. Women who want more comfort through the thigh.</p>

<h3>The Verdict</h3>
<p>If you want maximum leg-lengthening effect and do not mind a close fit — skinny. If you want a sleek silhouette with more comfort and versatility — slim. Most people who try both end up owning at least one pair of each.</p>

<p>Jeenzo carries both cuts in men\'s and women\'s, across 10+ washes. Both are available with free returns if the fit is not right.</p>',
                'tags'        => ['skinny jeans', 'slim fit', 'fit & comfort', 'men\'s fashion', 'women\'s fashion', 'jeans'],
                'is_featured' => true,
                'published_at'=> now()->subDays(7),
                'meta_title'  => 'Skinny vs Slim Fit Jeans — The Real Difference | Jeenzo',
                'meta_desc'   => 'Skinny and slim fit jeans are not the same. Jeenzo explains the actual difference in cut, fit and comfort — so you buy the right pair.',
            ],

            // ── Care & Maintain ───────────────────────────────────────

            [
                'title'       => 'How to Wash Your Jeans the Right Way (And How Often)',
                'category'    => 'Care & Maintain',
                'excerpt'     => 'Washing your jeans too often — or the wrong way — destroys the colour and the fabric. Here is the correct method to make your denim last for years.',
                'content'     => '<p>There is a widespread myth in fashion that you should never wash your jeans. That is an exaggeration — but the underlying point is correct: most people wash their jeans far too often and in ways that damage the fabric and fade the colour prematurely. Here is how to do it right.</p>

<h3>How Often Should You Wash Jeans?</h3>
<p>For everyday wear, washing every 5–10 wears is sufficient. Denim is naturally resilient — it does not absorb odour as quickly as cotton tees. The more you wash, the more the fibres break down and the colour fades. For raw denim specifically, many enthusiasts wait 3–6 months before the first wash to let the jeans develop unique fade patterns.</p>

<h3>The Right Way to Wash</h3>
<ul>
<li><strong>Turn inside out.</strong> This protects the outer face of the denim from friction and fading during the wash cycle.</li>
<li><strong>Cold water only.</strong> Hot water shrinks denim and accelerates colour fade. Always 30°C or below.</li>
<li><strong>Gentle cycle.</strong> Aggressive spin cycles stress the seams and the fabric. Use delicate or hand wash setting.</li>
<li><strong>Mild detergent.</strong> Avoid heavy-duty detergents with bleach agents. A small amount of mild liquid detergent is enough.</li>
<li><strong>Do not tumble dry.</strong> Heat is the enemy of denim. Air dry flat or hang by the waistband in shade.</li>
</ul>

<h3>Spot Cleaning</h3>
<p>For small stains, you do not need to wash the whole pair. Use a damp cloth with a tiny amount of soap, dab — do not rub — and let dry naturally. This preserves the rest of the denim.</p>

<h3>Removing Odour Without Washing</h3>
<p>Between wears, hang your jeans in fresh air for a few hours. You can also lightly mist the waistband and crotch with a diluted fabric refresher spray. Or: put your jeans in a zip-lock bag and freeze overnight — this kills odour-causing bacteria.</p>

<p>Treat your Jeenzo jeans well and they will last years. Our fabric is pre-washed to minimise shrinkage, but the care tips above apply to all quality denim.</p>',
                'tags'        => ['denim care', 'jeans', 'denim'],
                'is_featured' => false,
                'published_at'=> now()->subDays(18),
                'meta_title'  => 'How to Wash Jeans the Right Way | Denim Care Guide | Jeenzo',
                'meta_desc'   => 'Stop washing your jeans wrong. Jeenzo\'s complete denim care guide — how often to wash, cold water tips, air drying and odour removal.',
            ],

            [
                'title'       => '7 Ways to Make Your Jeans Last Longer',
                'category'    => 'Care & Maintain',
                'excerpt'     => 'Good jeans are an investment. With the right habits, a quality pair of denim can last 5–10 years. Here are seven practical tips to extend the life of your jeans.',
                'content'     => '<p>A quality pair of jeans from Jeenzo is built to last — but even the best denim needs a little help. These seven habits will significantly extend the life of your jeans and keep them looking great wash after wash.</p>

<h3>1. Wash Less</h3>
<p>We cannot say this enough. Every wash puts stress on denim fibres. Air out between wears, spot-clean where needed, and reserve a full wash for when the jeans genuinely need it.</p>

<h3>2. Always Cold Wash</h3>
<p>Hot water is the single biggest cause of colour fading and shrinkage. Cold water (30°C or below) cleans effectively without the damage.</p>

<h3>3. Skip the Dryer</h3>
<p>Tumble drying causes denim to shrink, warp at the seams, and lose elasticity in stretch fabric. Hang dry instead — flat if possible.</p>

<h3>4. Store Folded, Not Hung</h3>
<p>Hanging jeans by the waistband long-term can stretch the waistband and distort the shape. Fold them neatly on a shelf or in a drawer.</p>

<h3>5. Rotate Your Pairs</h3>
<p>Wearing the same pair every single day accelerates wear at friction points — the inner thigh, the back pockets, the hem. Rotating between two or three pairs extends each pair\'s life considerably.</p>

<h3>6. Fix Small Damage Early</h3>
<p>A small tear at a seam or a fraying hem is easy to fix early. Left alone, it becomes a much bigger problem. Take your jeans to a tailor at the first sign of seam stress.</p>

<h3>7. Use Denim-Specific Detergent</h3>
<p>Several brands make detergents specifically formulated for denim that clean without stripping colour. If you wear dark denim regularly, this is a worthwhile investment.</p>

<p>Jeans that are cared for properly can last a decade or more. The upfront investment in a quality pair from Jeenzo, paired with the right care, is far more economical — and better for the environment — than buying cheap jeans every year.</p>',
                'tags'        => ['denim care', 'denim', 'jeans', 'sustainable fashion'],
                'is_featured' => false,
                'published_at'=> now()->subDays(25),
                'meta_title'  => '7 Ways to Make Your Jeans Last Longer | Jeenzo Denim Care',
                'meta_desc'   => 'Make your jeans last years, not months. 7 practical denim care tips from Jeenzo — washing, drying, storage, and repair.',
            ],

            // ── Trends ────────────────────────────────────────────────

            [
                'title'       => 'Denim Trends in India 2025 — What\'s In and What\'s Out',
                'category'    => 'Trends',
                'excerpt'     => 'From wide-leg revivals to raw indigo, here is what Indian denim lovers are actually buying and wearing in 2025.',
                'content'     => '<p>Denim trends in India move differently from global runways — they are shaped by climate, occasion, and the practicality that Indian lifestyles demand. Here is an honest look at what is trending right now and what has run its course.</p>

<h3>IN: High-Rise Everything</h3>
<p>Low-rise had its moment in the early 2000s and briefly flirted with a comeback — but high-rise and mid-rise cuts have firmly taken over. They are more comfortable, more flattering for a wider range of body types, and easier to style. All Jeenzo cuts are mid to high rise.</p>

<h3>IN: Straight Leg Dominance</h3>
<p>The skinny jean is not dead, but the straight leg is the undisputed leader right now. Clean, versatile, worn by everyone from college students to corporate professionals. If you own only one pair of jeans, make it a straight leg.</p>

<h3>IN: Dark Indigo and Raw Denim</h3>
<p>The clean, rich look of dark indigo and raw unwashed denim is having a major moment. After years of lighter washes dominating, deep blues are back — and they work beautifully for both casual and semi-formal settings.</p>

<h3>IN: Relaxed and Loose Fits for Men</h3>
<p>The slim-fit monopoly in men\'s denim is easing. Relaxed fits — more room through the hip and thigh — are gaining ground, particularly among younger buyers who prefer a more laid-back silhouette.</p>

<h3>OUT: Heavily Distressed Denim</h3>
<p>Multiple rips, shredded knees, extreme whiskering — this look has peaked. The move is toward cleaner denim with more subtle, natural-looking distress if any.</p>

<h3>OUT: Super-Light Washes</h3>
<p>The almost-white bleached jeans look is fading. Medium to dark washes are dominant, and light washes that do sell are in soft ice-blue tones rather than stark white.</p>

<h3>ALWAYS IN: A Classic Dark-Wash Slim or Straight</h3>
<p>Some things do not change. A dark-wash slim or straight leg that can go from day to evening is the one piece of denim that will always be relevant. Buy the best quality you can afford.</p>',
                'tags'        => ['trends', 'denim', 'jeans', 'street style', 'men\'s fashion', 'women\'s fashion', 'straight leg', 'slim fit'],
                'is_featured' => true,
                'published_at'=> now()->subDays(3),
                'meta_title'  => 'Denim Trends in India 2025 — What\'s In and What\'s Out | Jeenzo',
                'meta_desc'   => 'What denim trends are Indians actually wearing in 2025? From straight legs to dark indigo, Jeenzo breaks down what\'s in and what\'s over.',
            ],

            [
                'title'       => 'How to Style Jeans for Indian Summers',
                'category'    => 'Trends',
                'excerpt'     => 'Wearing jeans through a 40°C Indian summer sounds miserable — but it does not have to be. Here\'s how to stay cool and look great.',
                'content'     => '<p>April through June in most of India is brutal. Yet jeans remain a staple even through summer — partly because of air-conditioned offices, partly because we simply love them. Here is how to make denim work in Indian heat.</p>

<h3>Fabric First</h3>
<p>Not all denim is equal in summer. Look for lighter-weight denim — under 10 oz — with a cotton-elastane blend that breathes. Jeenzo\'s summer range uses 9 oz cotton with 2% elastane, which is significantly more breathable than heavier winter denim.</p>

<h3>Light Washes</h3>
<p>In summer, lighter washes make psychological and practical sense. Ice blue, light indigo, and soft grey washes feel fresher and reflect more heat than dark washes.</p>

<h3>The Straight or Wide Leg Advantage</h3>
<p>A straight or wide leg jean has more airflow than a skinny or slim cut. For people who find denim uncomfortable in summer, switching from a skinny to a straight leg makes a genuine difference in how cool you feel.</p>

<h3>Loose Top Balance</h3>
<p>Pair summer jeans with loose, breathable tops — linen shirts, cotton kurtis, oversized tees. The volume on top compensates for the fitted nature of jeans. Avoid synthetic fabrics on top when wearing denim below.</p>

<h3>Keep It Light</h3>
<p>The combination of white or off-white top with light wash jeans is the ultimate Indian summer outfit. Minimal, clean, and refreshing to look at even on the hottest days.</p>

<h3>Cropped Lengths</h3>
<p>A cropped straight leg or high-water cut that ends just above the ankle improves airflow significantly and looks intentional. Roll up any full-length pair twice for the same effect.</p>

<p>Summer denim is not a contradiction — it is just about choosing the right weight, wash, and fit. Shop Jeenzo\'s summer denim collection now.</p>',
                'tags'        => ['summer denim', 'style tips', 'outfit ideas', 'straight leg', 'denim', 'trends'],
                'is_featured' => false,
                'published_at'=> now()->subDays(12),
                'meta_title'  => 'How to Wear Jeans in Indian Summer | Jeenzo Style Tips',
                'meta_desc'   => 'Stay cool in denim through Indian summers. Jeenzo\'s guide to the right fabric weight, washes, and fits for hot weather.',
            ],

            [
                'title'       => 'What to Wear to an Indian Wedding in Jeans',
                'category'    => 'Trends',
                'excerpt'     => 'Invited to a sangeet, mehendi or a casual wedding lunch and want to wear jeans? It is more than possible — here\'s how to do it right.',
                'content'     => '<p>Indian weddings are among the most elaborate events in the world — but not every function requires a full lehenga or sherwani. Sangeets, mehendis, ring ceremonies, and casual lunches are perfect occasions for elevated denim. Here is how to pull it off.</p>

<h3>For Women</h3>

<h4>Dark Slim + Embroidered Top</h4>
<p>A pair of dark slim or skinny jeans paired with an embroidered anarkali top, mirror-work blouse, or a statement sequined kurti elevates the entire look into wedding-appropriate territory. Add heeled sandals and statement earrings.</p>

<h4>Straight Leg + Silk Kurta</h4>
<p>A well-cut straight leg jean in dark indigo under a silk or brocade kurta is a popular choice at sangeet and mehendi functions. The jeans keep you comfortable for dancing — the kurta keeps you appropriately festive.</p>

<h4>Wide Leg + Crop Top</h4>
<p>For younger guests at casual functions, a wide-leg dark jean with a festive crop top in heavy fabric (zardozi, sequin, or brocade) reads fashion-forward and appropriate simultaneously.</p>

<h3>For Men</h3>

<h4>Dark Slim + Bandhgala</h4>
<p>A dark indigo slim fit with a structured bandhgala jacket is one of the sharpest semi-formal looks a man can put together. Add leather juttis or Chelsea boots.</p>

<h4>Straight Leg + Nehru Jacket</h4>
<p>A classic Nehru jacket over a plain kurta, tucked into dark straight leg jeans. Versatile enough for a mehendi or a casual baraati look at a smaller wedding.</p>

<h3>The Rules</h3>
<ul>
<li>Always dark wash — no light or distressed denim at a wedding.</li>
<li>The top must do the heavy lifting — it needs to read festive even if the jeans are neutral.</li>
<li>Clean sneakers are acceptable at casual functions. Formal functions require proper footwear.</li>
</ul>',
                'tags'        => ['festive fashion', 'outfit ideas', 'style tips', 'slim fit', 'straight leg', 'women\'s fashion', 'men\'s fashion'],
                'is_featured' => false,
                'published_at'=> now()->subDays(20),
                'meta_title'  => 'What to Wear to an Indian Wedding in Jeans | Jeenzo',
                'meta_desc'   => 'Wear jeans to a sangeet, mehendi or casual wedding lunch — and look perfectly appropriate. Jeenzo\'s festive denim style guide.',
            ],

            // ── Brand Story ───────────────────────────────────────────

            [
                'title'       => 'Why Jeenzo — Our Story, Our Denim, Our Promise',
                'category'    => 'Brand Story',
                'excerpt'     => 'Jeenzo was built on a simple belief: that every Indian deserves access to premium quality denim at an honest price. Here is where we started and where we are going.',
                'content'     => '<p>Jeenzo started with a very simple frustration: finding a great pair of jeans in India that fit well, lasted longer than a year, and did not cost as much as a month\'s rent.</p>

<p>Premium international brands are priced out of reach for most Indian buyers. The lower price point Indian alternatives have historically meant compromising on fabric quality, stitching, and fit — particularly for the Indian body, which often falls between the sizing assumptions built for Western markets.</p>

<p>Jeenzo was built to close that gap.</p>

<h3>The Denim</h3>
<p>We source our fabric from certified mills — premium cotton and cotton-elastane blends that balance structure with comfort. Every fabric we use is pre-washed and pre-shrunk, meaning what you receive fits consistently with what you see on the size chart. Our finishing uses double-needle stitching on stress points because that is what makes jeans last.</p>

<h3>The Fit</h3>
<p>We have done extensive fit testing across different body types common in India — accounting for the fact that Indian builds often have a wider hip-to-waist ratio than typical Western fit models. Our cuts are adapted accordingly. The result is a pair of jeans that does not gap at the back waistband when you sit down, and does not squeeze across the thigh when you have a mid-rise waist that fits perfectly.</p>

<h3>The Price</h3>
<p>We sell direct — no wholesalers, no middlemen, no retail overheads. That allows us to put the majority of our cost into the product itself and keep the price honest. A Jeenzo jean that would cost ₹4,000–5,000 at a mall store is available to you directly for significantly less, without any difference in quality.</p>

<h3>The Promise</h3>
<p>Every order from jeenzo.in ships with a 30-day fit guarantee. If the size is wrong, we exchange it. No complicated forms, no harassment. We stand behind our product because we know it is good.</p>

<p>Thank you for choosing Jeenzo. We are just getting started.</p>',
                'tags'        => ['denim', 'jeans', 'sustainable fashion', 'style tips'],
                'is_featured' => false,
                'published_at'=> now()->subDays(30),
                'meta_title'  => 'Our Story — Why Jeenzo | Premium Indian Denim Brand',
                'meta_desc'   => 'Jeenzo was built to give every Indian access to premium quality denim at an honest price. Learn about our fabric, fit philosophy and brand promise.',
            ],

        ];

        // ── INSERT POSTS ──────────────────────────────────────────────
        foreach ($posts as $data) {
            $post = BlogPost::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title'              => $data['title'],
                    'excerpt'            => $data['excerpt'],
                    'content'            => $data['content'],
                    'blog_category_id'   => $cats[$data['category']]->id,
                    'is_featured'        => $data['is_featured'],
                    'status'             => 'published',
                    'published_at'       => $data['published_at'],
                    'featured_image_url' => null,
                    'meta_title'         => $data['meta_title'],
                    'meta_description'   => $data['meta_desc'],
                    'canonical_url'      => 'https://jeenzo.in/blog/' . Str::slug($data['title']),
                    'og_title'           => $data['meta_title'],
                    'og_description'     => $data['meta_desc'],
                ]
            );

            // Attach tags
            $tagIds = collect($data['tags'])
                ->map(fn($t) => $tags[$t]->id ?? null)
                ->filter()
                ->toArray();

            $post->tags()->syncWithoutDetaching($tagIds);
        }

        // ── RELATED POSTS (cross-link thematically) ───────────────────
        $allPosts = BlogPost::all()->keyBy('slug');

        $related = [
            'how-to-style-slim-fit-jeans-for-every-occasion' => [
                'skinny-vs-slim-fit-jeans-whats-the-actual-difference',
                '5-ways-to-wear-straight-leg-jeans-this-season',
                'denim-trends-in-india-2025-whats-in-and-whats-out',
            ],
            'the-ultimate-guide-to-womens-jeans-fits-find-your-perfect-pair' => [
                'skinny-vs-slim-fit-jeans-whats-the-actual-difference',
                'how-to-find-your-perfect-jeans-size-the-jeenzo-sizing-guide',
                '5-ways-to-wear-straight-leg-jeans-this-season',
            ],
            'skinny-vs-slim-fit-jeans-whats-the-actual-difference' => [
                'the-ultimate-guide-to-womens-jeans-fits-find-your-perfect-pair',
                'how-to-find-your-perfect-jeans-size-the-jeenzo-sizing-guide',
                'how-to-style-slim-fit-jeans-for-every-occasion',
            ],
            'denim-trends-in-india-2025-whats-in-and-whats-out' => [
                'how-to-style-jeans-for-indian-summers',
                'what-to-wear-to-an-indian-wedding-in-jeans',
                '5-ways-to-wear-straight-leg-jeans-this-season',
            ],
        ];

        foreach ($related as $postSlug => $relatedSlugs) {
            if (!isset($allPosts[$postSlug])) continue;
            $post = $allPosts[$postSlug];
            $relatedIds = collect($relatedSlugs)
                ->map(fn($s) => $allPosts[$s]->id ?? null)
                ->filter()
                ->toArray();
            $post->relatedPosts()->syncWithoutDetaching($relatedIds);
        }

        $this->command->info('✅  BlogSeeder: ' . count($posts) . ' posts, ' . count($categories) . ' categories, ' . count($tagNames) . ' tags seeded.');
    }
}

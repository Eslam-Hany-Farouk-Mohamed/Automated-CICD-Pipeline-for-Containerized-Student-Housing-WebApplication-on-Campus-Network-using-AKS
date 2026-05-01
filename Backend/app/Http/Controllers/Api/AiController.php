<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\City;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array',
            'context.city_id' => 'nullable|exists:cities,id',
            'context.budget_min' => 'nullable|numeric',
            'context.budget_max' => 'nullable|numeric',
            'context.type' => 'nullable|in:room,apartment,studio,shared',
        ]);

        $user = $request->user();
        $message = $validated['message'];
        $context = $validated['context'] ?? [];
        $language = $user->language ?? 'ar';

        // Parse user message to extract criteria
        $extractedContext = $this->parseMessageForCriteria($message, $language);
        $context = array_merge($context, $extractedContext);

        // Generate response based on parsed criteria
        $response = $this->generateMockResponse($message, $context, $language);

        // Get related listings based on context
        $recommendedListings = $this->getRecommendedListings($context);

        return response()->json([
            'response' => $response['text'],
            'suggested_filters' => $response['filters'],
            'recommended_listings' => $recommendedListings,
        ]);
    }

    private function parseMessageForCriteria(string $message, string $language): array
    {
        $context = [];
        $lowerMessage = mb_strtolower($message);

        // Extract property type
        $types = ['room', 'apartment', 'studio', 'shared'];
        $typeKeywords = [
            'room' => ['room', 'غرفة', 'حجرة'],
            'apartment' => ['apartment', 'شقة', 'فلة'],
            'studio' => ['studio', 'ستوديو', 'استديو'],
            'shared' => ['shared', 'مشترك', 'جماعي'],
        ];

        foreach ($typeKeywords as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowerMessage, $keyword)) {
                    $context['type'] = $type;
                    break 2;
                }
            }
        }

        // Extract price/budget using regex
        preg_match('/(\d+)\s*(?:to|إلى|-)\s*(\d+)/', $message, $rangeMatches);
        if (!empty($rangeMatches)) {
            $context['budget_min'] = (int)$rangeMatches[1] * 100; // Assuming prices in hundreds
            $context['budget_max'] = (int)$rangeMatches[2] * 100;
        } else {
            preg_match('/(?:budget|price|سعر|ميزانية).*?(\d+)/', $message, $priceMatches);
            if (!empty($priceMatches)) {
                $context['budget_max'] = (int)$priceMatches[1] * 100;
            }
        }

        // Extract city name
        $cities = City::all();
        foreach ($cities as $city) {
            $cityNameEn = mb_strtolower($city->name);
            $cityNameAr = mb_strtolower($city->name_ar);
            
            if (str_contains($lowerMessage, $cityNameEn) || str_contains($lowerMessage, $cityNameAr)) {
                $context['city_id'] = $city->id;
                break;
            }
        }

        return $context;
    }

    private function generateMockResponse(string $message, array $context, string $language): array
    {
        $lang = $language === 'ar' ? 'ar' : 'en';
        $filters = [];
        
        // Build response based on extracted context
        if (!empty($context['city_id']) || !empty($context['type']) || !empty($context['budget_max'])) {
            // User provided search criteria
            $cityName = !empty($context['city_id']) ? City::find($context['city_id'])?->name : '';
            $typeName = $context['type'] ?? '';
            $maxPrice = $context['budget_max'] ?? '';
            
            if ($lang === 'ar') {
                $cityText = $cityName ? "في " . $cityName : "في أي مكان";
                $typeText = $typeName ? "من نوع " . $this->translateType($typeName, 'ar') : "";
                $priceText = $maxPrice ? "بسعر أقل من " . $maxPrice : "";
                
                $text = "ممتاز! لقد وجدت لك خيارات رائعة " . $cityText;
                if ($typeText) $text .= " " . $typeText;
                if ($priceText) $text .= " " . $priceText;
                $text .= ". أليك أفضل العروض المتاحة 👇";
            } else {
                $cityText = $cityName ? "in " . $cityName : "anywhere";
                $typeText = $typeName ? " - " . $this->translateType($typeName, 'en') : "";
                $priceText = $maxPrice ? " under EGP " . $maxPrice : "";
                
                $text = "Great! I found some excellent options for you " . $cityText . $typeText . $priceText . ". Here are the best available 👇";
            }
            
            // Add filters
            if (!empty($context['city_id'])) $filters['city_id'] = $context['city_id'];
            if (!empty($context['type'])) $filters['type'] = $context['type'];
            if (!empty($context['budget_max'])) $filters['price_max'] = $context['budget_max'];
            if (!empty($context['budget_min'])) $filters['price_min'] = $context['budget_min'];
        } else {
            // No specific criteria - offer help
            if ($lang === 'ar') {
                $text = "مرحباً بك! 🏠 يمكنني مساعدتك في البحث عن السكن المناسب. أخبرني:\n• المدينة التي تفضلها (مثل القاهرة أو الإسكندرية)\n• نوع السكن (غرفة، شقة، ستوديو، أو مشترك)\n• ميزانيتك\n\nوسأجد لك أفضل الخيارات! 😊";
            } else {
                $text = "Hello! 🏠 I can help you find the perfect housing. Tell me:\n• Your preferred city (like Cairo or Alexandria)\n• Type of accommodation (room, apartment, studio, or shared)\n• Your budget\n\nAnd I'll find you the best options! 😊";
            }
        }

        return [
            'text' => $text,
            'filters' => $filters,
        ];
    }

    private function translateType(string $type, string $language): string
    {
        $translations = [
            'ar' => [
                'room' => 'غرفة',
                'apartment' => 'شقة',
                'studio' => 'ستوديو',
                'shared' => 'مشترك',
            ],
            'en' => [
                'room' => 'Room',
                'apartment' => 'Apartment',
                'studio' => 'Studio',
                'shared' => 'Shared',
            ],
        ];
        
        return $translations[$language][$type] ?? $type;
    }

    private function getRecommendedListings(array $context): array
    {
        $query = Listing::with(['city', 'region', 'images'])
            ->where('is_available', true);

        // Apply filters if provided
        if (!empty($context['city_id'])) {
            $query->where('city_id', $context['city_id']);
        }

        if (!empty($context['budget_min'])) {
            $query->where('price', '>=', $context['budget_min']);
        }

        if (!empty($context['budget_max'])) {
            $query->where('price', '<=', $context['budget_max']);
        }

        if (!empty($context['type'])) {
            $query->where('type', $context['type']);
        }

        // Always return at least some results
        $results = $query->limit(5)->get();
        
        // If no results found with filters, return random listings
        if ($results->isEmpty()) {
            $results = Listing::with(['city', 'region', 'images'])
                ->where('is_available', true)
                ->inRandomOrder()
                ->limit(5)
                ->get();
        }

        return $results->toArray();
    }

    public function supportChat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $message = $validated['message'];
        $language = $user->language ?? 'ar';

        $responses = [
            'ar' => [
                'empathy' => [
                    'أنا أتفهم تماماً ما تمر به. من الشجاع جداً أن تشارك مشاعرك.',
                    'أنا أسمعك، ومن الطبيعي جداً أن تشعر هكذا أحياناً. أنا هنا معك.',
                    'شكراً لأنك وثقت بي وشاركتني هذا. تذكر أن هذه المشاعر مؤقتة وستمر.',
                ],
                'academic' => [
                    'الضغط الدراسي يمكن أن يكون مرهقاً، لكن تذكر أنك لست وحدك في هذا.',
                    'الدراسة رحلة طويلة، ومن الطبيعي أن تشعر بالتعب أحياناً. خذ قسطاً من الراحة.',
                    'أنت تبذل جهداً رائعاً. لا تنسَ أن تعتني بنفسك بجانب المذاكرة.',
                ],
                'lonely' => [
                    'الشعور بالوحدة صعب، لكننا هنا لدعمك. هل ترغب في اقتراح لبعض الأنشطة الاجتماعية؟',
                    'وحدتك ليست دائمة، هناك الكثير من الزملاء الذين يشعرون بمثلك. جرب التواصل مع أحدهم اليوم.',
                    'تذكر أنني هنا دائماً لأتحدث معك. لا تتردد في مشاركة أي شيء يدور في ذهنك.',
                ],
                'greeting' => [
                    'أهلاً بك في غرفة الشجعان! كيف يمكنني دعمك اليوم؟',
                    'مرحباً بك، أنا هنا لأسمعك وأكون بجانبك. كيف حالك؟',
                    'أهلاً صديقي، سعيد جداً بوجودك هنا. ماذا يدور في بالك؟',
                ],
                'status' => [
                    'أنا بخير، شكراً لسؤالك! كيف حالك أنت؟',
                    'أنا هنا وجاهز لدعمك دائماً. كيف يسير يومك؟',
                    'بخير طالما أنني أستطيع مساعدتك. أخبرني عنك.',
                ],
                'default' => [
                    'شكراً لمشاركة ذلك. أنا هنا لأسمعك وأدعمك دائماً.',
                    'أنا معك، استمر في التعبير عما بداخلك.',
                    'أقدر صدقك وشجاعتك في الحديث. أخبرني المزيد إذا أردت.',
                ],
            ],
            'en' => [
                'empathy' => [
                    "I truly understand what you're going through. It's very brave of you to share your feelings.",
                    "I hear you, and it's completely normal to feel this way sometimes. I'm right here with you.",
                    "Thank you for trusting me with this. Remember, these feelings are temporary and will pass.",
                ],
                'academic' => [
                    "Academic pressure can be overwhelming, but remember you're not alone in this.",
                    "Studying is a long journey, and it's okay to feel tired sometimes. Make sure to take a break.",
                    "You're doing a great job. Don't forget to take care of yourself while pursuing your goals.",
                ],
                'lonely' => [
                    "Feeling lonely is tough, but we're here to support you. Would you like some social activity suggestions?",
                    "Your loneliness isn't permanent. Many others feel the same way. Try reaching out to someone today.",
                    "Remember, I'm always here to talk. Please don't hesitate to share whatever is on your mind.",
                ],
                'greeting' => [
                    "Welcome to the Brave Room! How can I support you today?",
                    "Hello! I'm here to listen and be by your side. How are you doing?",
                    "Hi there, my friend. I'm glad you're here. What's on your mind?",
                ],
                'status' => [
                    "I'm doing well, thank you for asking! How are you?",
                    "I'm here and always ready to support you. How is your day going?",
                    "I'm good as long as I can help you. Tell me about yourself.",
                ],
                'default' => [
                    "Thank you for sharing that. I'm here to listen and support you always.",
                    "I'm with you. Keep expressing what's inside.",
                    "I appreciate your honesty. Tell me more, I'm listening.",
                ],
            ],
        ];

        $lang = $language === 'ar' ? 'ar' : 'en';
        $category = 'default';
        $lowerMessage = mb_strtolower($message);

        // Keyword detection
        if (str_contains($lowerMessage, 'hello') || str_contains($lowerMessage, 'hi') || str_contains($lowerMessage, 'مرحبا') || str_contains($lowerMessage, 'أهلا')) {
            $category = 'greeting';
        }
        elseif (str_contains($lowerMessage, 'كيف حالك') || str_contains($lowerMessage, 'how are you') || str_contains($lowerMessage, 'أخبارك') || str_contains($lowerMessage, 'how r u')) {
            $category = 'status';
        }
        elseif (str_contains($lowerMessage, 'تعب') || str_contains($lowerMessage, 'tired') || str_contains($lowerMessage, 'sad') || str_contains($lowerMessage, 'حزين') || str_contains($lowerMessage, 'pain')) {
            $category = 'empathy';
        }
        elseif (str_contains($lowerMessage, 'study') || str_contains($lowerMessage, 'دراسة') || str_contains($lowerMessage, 'امتحان') || str_contains($lowerMessage, 'exam') || str_contains($lowerMessage, 'درجات')) {
            $category = 'academic';
        }
        elseif (str_contains($lowerMessage, 'alone') || str_contains($lowerMessage, 'وحدي') || str_contains($lowerMessage, 'lonely') || str_contains($lowerMessage, 'وحيد')) {
            $category = 'lonely';
        }

        $options = $responses[$lang][$category];
        $text = $options[array_rand($options)];

        return response()->json([
            'response' => $text,
        ]);
    }

    public function resolveConflict(Request $request)
    {
        $validated = $request->validate([
            'problem' => 'required|string|max:1000',
        ]);

        $user = $request->user();
        $problem = $validated['problem'];
        $language = $user->language ?? 'ar';

        $responses = [
            'ar' => [
                'noise' => 'لحل مشكلة الضوضاء، نقترح: 1. تحديد ساعات هدوء متفق عليها. 2. استخدام سدادات الأذن أو سماعات إلغاء الضوضاء. 3. التحدث بهدوء مع زميلك حول تأثير ذلك على دراستك.',
                'cleaning' => 'لحل مشكلة النظافة: 1. قم بإنشاء جدول تنظيف أسبوعي معلق على الثلاجة. 2. تقسيم المهام بشكل عادل. 3. الاتفاق على غسل الأطباق فوراً بعد الاستخدام.',
                'guest' => 'مشاكل الضيوف: 1. الاتفاق على عدد مرات الزيارة المسموحة. 2. طلب الإذن قبل دعوة الضيوف للمبيت. 3. احترام المساحة الشخصية لكل زميل.',
                'default' => 'أفضل حل لأي صراع هو الحوار الهادئ والواضح. حاول الجلوس مع زميلك ووضع "ميثاق سكن" يرضي الطرفين.',
            ],
            'en' => [
                'noise' => 'To solve noise issues: 1. Set agreed quiet hours. 2. Use earplugs or noise-canceling headphones. 3. Talk calmly with your roommate about how it affects your studies.',
                'cleaning' => 'To solve cleanliness issues: 1. Create a weekly cleaning schedule on the fridge. 2. Divide tasks fairly. 3. Agree on washing dishes immediately after use.',
                'guest' => 'Guest issues: 1. Agree on allowed visit frequency. 2. Ask for permission before inviting overnight guests. 3. Respect each other\'s personal space.',
                'default' => 'The best solution for any conflict is calm and clear communication. Try sitting down with your roommate to create a "Housing Contract" that satisfies both parties.',
            ],
        ];

        $lang = $language === 'ar' ? 'ar' : 'en';
        $solution = $responses[$lang]['default'];
        $lowerProblem = mb_strtolower($problem);

        if (str_contains($lowerProblem, 'noise') || str_contains($lowerProblem, 'صوت') || str_contains($lowerProblem, 'إزعاج') || str_contains($lowerProblem, 'loud')) {
            $solution = $responses[$lang]['noise'];
        }
        elseif (str_contains($lowerProblem, 'clean') || str_contains($lowerProblem, 'نظافة') || str_contains($lowerProblem, 'dirty') || str_contains($lowerProblem, 'وسخ')) {
            $solution = $responses[$lang]['cleaning'];
        }
        elseif (str_contains($lowerProblem, 'guest') || str_contains($lowerProblem, 'ضيف') || str_contains($lowerProblem, 'أصدقاء') || str_contains($lowerProblem, 'visit')) {
            $solution = $responses[$lang]['guest'];
        }

        return response()->json([
            'solution' => $solution,
        ]);
    }
}

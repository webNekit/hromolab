<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Auth\Models\Profile;
use App\Domains\Auth\Models\User;
use App\Domains\Catalog\Models\Analysis;
use App\Domains\Catalog\Models\Category;
use App\Domains\Laboratories\Models\Laboratory;
use App\Domains\Laboratories\Models\LaboratoryWorkingHour;
use App\Domains\Orders\Models\Order;
use App\Domains\Orders\Models\OrderItem;
use App\Domains\Orders\Models\OrderStatusHistory;
use App\Domains\Orders\Models\PromoCode;
use App\Domains\Results\Models\MedicalResult;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private array $labAssistantEmails = [
        'lab.ivanova@chromolab.ru',
        'lab.petrov@chromolab.ru',
        'lab.sidorova@chromolab.ru',
        'lab.kuznetsov@chromolab.ru',
        'lab.popova@chromolab.ru',
    ];

    public function run(): void
    {
        $this->createRoles();
        $this->createAdmin();
        $this->createLabAssistants();
        $this->createCategoriesAndAnalyses();
        $this->createLaboratories();
        $this->createPromoCodes();
        $this->createOrders();
    }

    private function createRoles(): void
    {
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'lab-assistant', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
    }

    private function createAdmin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'sagaevr10@gmail.com'],
            [
                'name' => 'Никита Сагаев',
                'phone' => '+7 (999) 999-99-99',
                'password' => bcrypt('password'),
            ],
        );
        $admin->assignRole('super-admin');

        Profile::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'first_name' => 'Никита',
                'last_name' => 'Сагаев',
                'birth_date' => '1995-06-15',
                'gender' => 'male',
            ],
        );
    }

    private function createLabAssistants(): void
    {
        $assistants = [
            ['name' => 'Анна Иванова', 'phone' => '+7 (901) 111-11-11', 'first_name' => 'Анна', 'last_name' => 'Иванова', 'gender' => 'female'],
            ['name' => 'Пётр Петров', 'phone' => '+7 (902) 222-22-22', 'first_name' => 'Пётр', 'last_name' => 'Петров', 'gender' => 'male'],
            ['name' => 'Мария Сидорова', 'phone' => '+7 (903) 333-33-33', 'first_name' => 'Мария', 'last_name' => 'Сидорова', 'gender' => 'female'],
            ['name' => 'Алексей Кузнецов', 'phone' => '+7 (904) 444-44-44', 'first_name' => 'Алексей', 'last_name' => 'Кузнецов', 'gender' => 'male'],
            ['name' => 'Елена Попова', 'phone' => '+7 (905) 555-55-55', 'first_name' => 'Елена', 'last_name' => 'Попова', 'gender' => 'female'],
        ];

        foreach ($assistants as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $this->labAssistantEmails[$i]],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => bcrypt('password'),
                ],
            );
            $user->assignRole('lab-assistant');

            Profile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'birth_date' => fake()->date('Y-m-d', '-25 years'),
                    'gender' => $data['gender'],
                ],
            );
        }
    }

    private function createCategoriesAndAnalyses(): void
    {
        $categories = [
            [
                'name' => 'Общеклинические исследования',
                'slug' => 'obshcheklinicheskie-issledovaniya',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Общий анализ крови', 'slug' => 'obshchiy-analiz-krovi', 'sort_order' => 1],
                    ['name' => 'Биохимический анализ крови', 'slug' => 'biohimicheskiy-analiz-krovi', 'sort_order' => 2],
                    ['name' => 'Общий анализ мочи', 'slug' => 'obshchiy-analiz-mochi', 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Гормональные исследования',
                'slug' => 'gormonalnye-issledovaniya',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Щитовидная железа', 'slug' => 'shchitovidnaya-zheleza', 'sort_order' => 1],
                    ['name' => 'Половые гормоны', 'slug' => 'polovye-gormony', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Инфекционные заболевания',
                'slug' => 'infektsionnye-zabolevaniya',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'ИППП', 'slug' => 'ippp', 'sort_order' => 1],
                    ['name' => 'Вирусные инфекции', 'slug' => 'virusnye-infektsii', 'sort_order' => 2],
                    ['name' => 'TORCH-инфекции', 'slug' => 'torch-infektsii', 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Онкомаркеры',
                'slug' => 'onkomarkery',
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Женские онкомаркеры', 'slug' => 'zhenskie-onkomarkery', 'sort_order' => 1],
                    ['name' => 'Мужские онкомаркеры', 'slug' => 'muzhskie-onkomarkery', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Иммунология и аллергология',
                'slug' => 'immunologiya-i-allergologiya',
                'sort_order' => 5,
                'children' => [
                    ['name' => 'Иммунный статус', 'slug' => 'immunnyy-status', 'sort_order' => 1],
                    ['name' => 'Аллергопанели', 'slug' => 'allergopanei', 'sort_order' => 2],
                ],
            ],
        ];

        $analyses = [
            ['name' => 'Гемоглобин', 'sku' => 'HL-0001', 'biomaterial' => 'Кровь капиллярная', 'price' => 350, 'lead_time_days' => 1, 'is_popular' => true, 'category_idx' => [0, 0], 'reference' => ['hemoglobin' => ['min' => 120, 'max' => 160, 'unit' => 'g/L']]],
            ['name' => 'Эритроциты (RBC)', 'sku' => 'HL-0002', 'biomaterial' => 'Кровь капиллярная', 'price' => 300, 'lead_time_days' => 1, 'category_idx' => [0, 0], 'reference' => ['rbc' => ['min' => 3.8, 'max' => 5.3, 'unit' => 'x10^12/L']]],
            ['name' => 'Лейкоциты (WBC)', 'sku' => 'HL-0003', 'biomaterial' => 'Кровь капиллярная', 'price' => 300, 'lead_time_days' => 1, 'is_popular' => true, 'category_idx' => [0, 0], 'reference' => ['wbc' => ['min' => 4.0, 'max' => 9.0, 'unit' => 'x10^9/L']]],
            ['name' => 'Тромбоциты (PLT)', 'sku' => 'HL-0004', 'biomaterial' => 'Кровь капиллярная', 'price' => 300, 'lead_time_days' => 1, 'category_idx' => [0, 0], 'reference' => ['plt' => ['min' => 180, 'max' => 320, 'unit' => 'x10^9/L']]],
            ['name' => 'Лейкоцитарная формула', 'sku' => 'HL-0005', 'biomaterial' => 'Кровь капиллярная', 'price' => 500, 'lead_time_days' => 1, 'category_idx' => [0, 0]],
            ['name' => 'СОЭ (ESR)', 'sku' => 'HL-0006', 'biomaterial' => 'Кровь венозная', 'price' => 280, 'lead_time_days' => 1, 'is_popular' => true, 'category_idx' => [0, 0], 'reference' => ['esr' => ['min' => 0, 'max' => 15, 'unit' => 'mm/h']]],
            ['name' => 'Глюкоза', 'sku' => 'HL-0007', 'biomaterial' => 'Сыворотка крови', 'price' => 320, 'lead_time_days' => 1, 'is_popular' => true, 'category_idx' => [0, 1], 'reference' => ['glucose' => ['min' => 3.3, 'max' => 5.5, 'unit' => 'mmol/L']]],
            ['name' => 'Общий холестерин', 'sku' => 'HL-0008', 'biomaterial' => 'Сыворотка крови', 'price' => 350, 'lead_time_days' => 1, 'category_idx' => [0, 1], 'reference' => ['cholesterol' => ['min' => 3.6, 'max' => 5.2, 'unit' => 'mmol/L']]],
            ['name' => 'ЛПНП (Холестерин липопротеинов низкой плотности)', 'sku' => 'HL-0009', 'biomaterial' => 'Сыворотка крови', 'price' => 400, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'ЛПВП (Холестерин липопротеинов высокой плотности)', 'sku' => 'HL-0010', 'biomaterial' => 'Сыворотка крови', 'price' => 400, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Триглицериды', 'sku' => 'HL-0011', 'biomaterial' => 'Сыворотка крови', 'price' => 380, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'АЛТ (Аланинаминотрансфераза)', 'sku' => 'HL-0012', 'biomaterial' => 'Сыворотка крови', 'price' => 320, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'АСТ (Аспартатаминотрансфераза)', 'sku' => 'HL-0013', 'biomaterial' => 'Сыворотка крови', 'price' => 320, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Билирубин общий', 'sku' => 'HL-0014', 'biomaterial' => 'Сыворотка крови', 'price' => 300, 'lead_time_days' => 1, 'category_idx' => [0, 1], 'reference' => ['bilirubin' => ['min' => 3.4, 'max' => 20.5, 'unit' => 'mcmol/L']]],
            ['name' => 'Креатинин', 'sku' => 'HL-0015', 'biomaterial' => 'Сыворотка крови', 'price' => 310, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Мочевина', 'sku' => 'HL-0016', 'biomaterial' => 'Сыворотка крови', 'price' => 290, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Общий белок', 'sku' => 'HL-0017', 'biomaterial' => 'Сыворотка крови', 'price' => 280, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Альбумин', 'sku' => 'HL-0018', 'biomaterial' => 'Сыворотка крови', 'price' => 300, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Мочевая кислота', 'sku' => 'HL-0019', 'biomaterial' => 'Сыворотка крови', 'price' => 310, 'lead_time_days' => 1, 'category_idx' => [0, 1]],
            ['name' => 'Анализ мочи общий', 'sku' => 'HL-0020', 'biomaterial' => 'Моча', 'price' => 400, 'lead_time_days' => 1, 'is_popular' => true, 'category_idx' => [0, 2]],
            ['name' => 'ТТГ (Тиреотропный гормон)', 'sku' => 'HL-0021', 'biomaterial' => 'Сыворотка крови', 'price' => 550, 'lead_time_days' => 2, 'is_popular' => true, 'category_idx' => [1, 0]],
            ['name' => 'Т3 свободный', 'sku' => 'HL-0022', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 2, 'category_idx' => [1, 0]],
            ['name' => 'Т4 свободный', 'sku' => 'HL-0023', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 2, 'category_idx' => [1, 0]],
            ['name' => 'Антитела к ТПО', 'sku' => 'HL-0024', 'biomaterial' => 'Сыворотка крови', 'price' => 750, 'lead_time_days' => 3, 'category_idx' => [1, 0]],
            ['name' => 'Антитела к ТГ', 'sku' => 'HL-0025', 'biomaterial' => 'Сыворотка крови', 'price' => 750, 'lead_time_days' => 3, 'category_idx' => [1, 0]],
            ['name' => 'Эстрадиол (E2)', 'sku' => 'HL-0026', 'biomaterial' => 'Сыворотка крови', 'price' => 650, 'lead_time_days' => 3, 'category_idx' => [1, 1]],
            ['name' => 'Прогестерон', 'sku' => 'HL-0027', 'biomaterial' => 'Сыворотка крови', 'price' => 650, 'lead_time_days' => 3, 'category_idx' => [1, 1]],
            ['name' => 'Тестостерон общий', 'sku' => 'HL-0028', 'biomaterial' => 'Сыворотка крови', 'price' => 700, 'lead_time_days' => 3, 'category_idx' => [1, 1]],
            ['name' => 'Пролактин', 'sku' => 'HL-0029', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 2, 'category_idx' => [1, 1]],
            ['name' => 'ФСГ (Фолликулостимулирующий гормон)', 'sku' => 'HL-0030', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 2, 'category_idx' => [1, 1]],
            ['name' => 'Сифилис (RPR)', 'sku' => 'HL-0031', 'biomaterial' => 'Сыворотка крови', 'price' => 500, 'lead_time_days' => 3, 'category_idx' => [2, 0]],
            ['name' => 'ВИЧ (антитела)', 'sku' => 'HL-0032', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 3, 'is_popular' => true, 'category_idx' => [2, 0]],
            ['name' => 'Гепатит B (HBsAg)', 'sku' => 'HL-0033', 'biomaterial' => 'Сыворотка крови', 'price' => 550, 'lead_time_days' => 2, 'category_idx' => [2, 0]],
            ['name' => 'Гепатит C (антитела)', 'sku' => 'HL-0034', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 2, 'category_idx' => [2, 0]],
            ['name' => 'Хламидия трахоматис (ПЦР)', 'sku' => 'HL-0035', 'biomaterial' => 'Мазок', 'price' => 450, 'lead_time_days' => 2, 'category_idx' => [2, 0]],
            ['name' => 'Гонорея (ПЦР)', 'sku' => 'HL-0036', 'biomaterial' => 'Мазок', 'price' => 450, 'lead_time_days' => 2, 'category_idx' => [2, 0]],
            ['name' => 'Корь (IgG)', 'sku' => 'HL-0037', 'biomaterial' => 'Сыворотка крови', 'price' => 700, 'lead_time_days' => 4, 'category_idx' => [2, 1]],
            ['name' => 'Краснуха (IgG)', 'sku' => 'HL-0038', 'biomaterial' => 'Сыворотка крови', 'price' => 650, 'lead_time_days' => 3, 'category_idx' => [2, 1]],
            ['name' => 'Цитомегаловирус (IgG)', 'sku' => 'HL-0039', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 3, 'category_idx' => [2, 1]],
            ['name' => 'SARS-CoV-2 (COVID-19, IgG)', 'sku' => 'HL-0040', 'biomaterial' => 'Сыворотка крови', 'price' => 800, 'lead_time_days' => 2, 'category_idx' => [2, 1]],
            ['name' => 'Токсоплазма (IgG)', 'sku' => 'HL-0041', 'biomaterial' => 'Сыворотка крови', 'price' => 650, 'lead_time_days' => 3, 'category_idx' => [2, 2]],
            ['name' => 'Герпес HSV 1/2 (IgG)', 'sku' => 'HL-0042', 'biomaterial' => 'Сыворотка крови', 'price' => 600, 'lead_time_days' => 3, 'category_idx' => [2, 2]],
            ['name' => 'CA-125 (онкомаркер яичников)', 'sku' => 'HL-0043', 'biomaterial' => 'Сыворотка крови', 'price' => 1200, 'lead_time_days' => 3, 'category_idx' => [3, 0]],
            ['name' => 'CA-15-3 (онкомаркер молочной железы)', 'sku' => 'HL-0044', 'biomaterial' => 'Сыворотка крови', 'price' => 1300, 'lead_time_days' => 3, 'category_idx' => [3, 0]],
            ['name' => 'ПСА общий (Простатспецифический антиген)', 'sku' => 'HL-0045', 'biomaterial' => 'Сыворотка крови', 'price' => 1100, 'lead_time_days' => 2, 'is_popular' => true, 'category_idx' => [3, 1], 'reference' => ['psa' => ['min' => 0, 'max' => 4, 'unit' => 'ng/mL']]],
            ['name' => 'ПСА свободный', 'sku' => 'HL-0046', 'biomaterial' => 'Сыворотка крови', 'price' => 1300, 'lead_time_days' => 3, 'category_idx' => [3, 1]],
            ['name' => 'Иммуноглобулин E (IgE) общий', 'sku' => 'HL-0047', 'biomaterial' => 'Сыворотка крови', 'price' => 900, 'lead_time_days' => 3, 'category_idx' => [4, 0]],
            ['name' => 'Иммуноглобулин A (IgA)', 'sku' => 'HL-0048', 'biomaterial' => 'Сыворотка крови', 'price' => 800, 'lead_time_days' => 3, 'category_idx' => [4, 0]],
            ['name' => 'Аллергопанель "Бытовые аллергены"', 'sku' => 'HL-0049', 'biomaterial' => 'Сыворотка крови', 'price' => 2500, 'lead_time_days' => 7, 'category_idx' => [4, 1]],
            ['name' => 'Аллергопанель "Пищевые аллергены"', 'sku' => 'HL-0050', 'biomaterial' => 'Сыворотка крови', 'price' => 3500, 'lead_time_days' => 7, 'category_idx' => [4, 1]],
        ];

        $createdCategories = [];

        foreach ($categories as $parentData) {
            $parent = Category::firstOrCreate(
                ['slug' => $parentData['slug']],
                [
                    'name' => $parentData['name'],
                    'sort_order' => $parentData['sort_order'],
                    'is_active' => true,
                ],
            );
            $createdCategories[] = $parent;

            foreach ($parentData['children'] as $childData) {
                $child = Category::firstOrCreate(
                    ['slug' => $childData['slug']],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childData['name'],
                        'sort_order' => $childData['sort_order'],
                        'is_active' => true,
                    ],
                );
                $createdCategories[] = $child;
            }
        }

        foreach ($analyses as $data) {
            [$parentIdx, $childIdx] = $data['category_idx'];
            $categoryPath = $categories[$parentIdx]['children'];
            $childSlug = $categoryPath[$childIdx]['slug'];
            $category = Category::where('slug', $childSlug)->first();

            if ($category === null) {
                continue;
            }

            $referenceRanges = $data['reference'] ?? null;

            Analysis::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']),
                    'description' => fake()->optional(0.7)->sentence(10),
                    'preparation' => fake()->optional(0.5)->sentence(8),
                    'biomaterial' => $data['biomaterial'],
                    'price' => $data['price'],
                    'lead_time_days' => $data['lead_time_days'],
                    'reference_ranges' => $referenceRanges,
                    'is_active' => true,
                    'is_popular' => $data['is_popular'] ?? false,
                ],
            );
        }
    }

    private function createLaboratories(): void
    {
        $labs = [
            ['name' => 'Хромолаб — Центральный офис', 'address' => 'г. Москва, ул. Тверская, д. 10', 'phone' => '+7 (495) 111-11-11', 'lat' => 55.7658, 'lon' => 37.6064],
            ['name' => 'Хромолаб — Южное Бутово', 'address' => 'г. Москва, ул. Академика Янгеля, д. 5', 'phone' => '+7 (495) 222-22-22', 'lat' => 55.5458, 'lon' => 37.5436],
            ['name' => 'Хромолаб — Северное Медведково', 'address' => 'г. Москва, ул. Широкая, д. 15', 'phone' => '+7 (495) 333-33-33', 'lat' => 55.8788, 'lon' => 37.6688],
            ['name' => 'Хромолаб — Новокосино', 'address' => 'г. Москва, ул. Новокосинская, д. 20', 'phone' => '+7 (495) 444-44-44', 'lat' => 55.7418, 'lon' => 37.8568],
            ['name' => 'Хромолаб — Солнцево', 'address' => 'г. Москва, ул. Авиаторов, д. 8', 'phone' => '+7 (495) 555-55-55', 'lat' => 55.6458, 'lon' => 37.4068],
        ];

        foreach ($labs as $data) {
            $lab = Laboratory::firstOrCreate(
                ['name' => $data['name']],
                [
                    'address' => $data['address'],
                    'latitude' => $data['lat'],
                    'longitude' => $data['lon'],
                    'phone' => $data['phone'],
                    'email' => 'info@'.strtolower(str_replace([' ', '–', '—'], '', Str::transliterate($data['name']))).'.ru',
                    'is_active' => true,
                ],
            );

            // Create working hours for each day of the week
            $dayConfigs = [
                [1, '07:00', '20:00', 15], // Monday
                [2, '07:00', '20:00', 15], // Tuesday
                [3, '07:00', '20:00', 15], // Wednesday
                [4, '07:00', '20:00', 15], // Thursday
                [5, '07:00', '20:00', 15], // Friday
                [6, '08:00', '18:00', 15], // Saturday
                [0, '09:00', '16:00', 30], // Sunday
            ];

            foreach ($dayConfigs as [$day, $open, $close, $interval]) {
                LaboratoryWorkingHour::firstOrCreate(
                    [
                        'laboratory_id' => $lab->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'open_time' => $open,
                        'close_time' => $close,
                        'slot_interval_minutes' => $interval,
                    ],
                );
            }
        }
    }

    private function createPromoCodes(): void
    {
        $codes = [
            ['code' => 'WELCOME10', 'discount_type' => 'percent', 'discount_value' => 10, 'max_uses' => 100],
            ['code' => 'BONUS500', 'discount_type' => 'fixed', 'discount_value' => 500, 'max_uses' => 50],
            ['code' => 'PROMO20', 'discount_type' => 'percent', 'discount_value' => 20, 'max_uses' => 30],
            ['code' => 'HEALTH2026', 'discount_type' => 'percent', 'discount_value' => 15, 'max_uses' => 200],
            ['code' => 'FIRST1000', 'discount_type' => 'fixed', 'discount_value' => 1000, 'max_uses' => 10],
        ];

        foreach ($codes as $data) {
            PromoCode::firstOrCreate(
                ['code' => $data['code']],
                [
                    'discount_type' => $data['discount_type'],
                    'discount_value' => $data['discount_value'],
                    'max_uses' => $data['max_uses'],
                    'is_active' => true,
                    'valid_from' => now()->subMonth(),
                    'valid_until' => now()->addYear(),
                ],
            );
        }
    }

    private function createOrders(): void
    {
        $patients = User::factory(30)->create();
        foreach ($patients as $patient) {
            $patient->assignRole('patient');
            Profile::factory()->create(['user_id' => $patient->id]);
        }

        $allPatients = User::role('patient')->get();
        $laboratories = Laboratory::all();
        $analyses = Analysis::all();
        $labAssistants = User::role('lab-assistant')->get();

        $statuses = ['new', 'processing', 'ready_for_lab', 'analyzing', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'paid', 'paid', 'refunded'];

        for ($i = 0; $i < 100; $i++) {
            $patient = $allPatients->random();
            $laboratory = $laboratories->random();
            $appointmentDate = fake()->dateTimeBetween('-60 days', '+14 days');
            $status = $statuses[array_rand($statuses)];
            $paymentStatus = match ($status) {
                'completed', 'cancelled' => 'paid',
                'new' => 'pending',
                default => $paymentStatuses[array_rand($paymentStatuses)],
            };

            $analysesInOrder = $analyses->random(fake()->numberBetween(1, 5));
            $totalPrice = (float) $analysesInOrder->sum('price');

            $order = Order::create([
                'user_id' => $patient->id,
                'laboratory_id' => $laboratory->id,
                'order_number' => 'HL-'.fake()->unique()->numerify('######'),
                'appointment_datetime' => $appointmentDate,
                'total_price' => $totalPrice,
                'payment_status' => $paymentStatus,
                'current_status' => $status,
                'promo_code' => fake()->optional(0.2)->randomElement(['WELCOME10', 'BONUS500', null]),
            ]);

            // Record status history
            $historyStatuses = $this->generateStatusHistory($status);

            foreach ($historyStatuses as $j => $histStatus) {
                $changedBy = match ($histStatus) {
                    'ready_for_lab', 'analyzing', 'completed' => $labAssistants->random()->id,
                    default => null,
                };

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'changed_by_user_id' => $changedBy,
                    'status' => $histStatus,
                    'previous_status' => $j > 0 ? $historyStatuses[$j - 1] : null,
                    'comment' => match ($histStatus) {
                        'completed' => 'Все результаты готовы',
                        'cancelled' => 'Отменён пациентом',
                        'ready_for_lab' => 'Заказ готов к проведению исследований',
                        'analyzing' => 'Начало выполнения анализов',
                        default => null,
                    },
                    'created_at' => Carbon::parse($order->created_at)->addMinutes($j * fake()->numberBetween(30, 1440)),
                ]);
            }

            // Create order items
            foreach ($analysesInOrder as $analysis) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'analysis_id' => $analysis->id,
                    'price' => $analysis->price,
                    'discount' => 0,
                ]);

                // Create medical results for completed orders
                if ($status === 'completed') {
                    $labAssistant = $labAssistants->random();
                    $parameters = $this->generateParameterValues($analysis);

                    MedicalResult::create([
                        'order_item_id' => $orderItem->id,
                        'lab_assistant_id' => $labAssistant->id,
                        'parameter_values' => $parameters,
                        'pdf_path' => 'medical_results/'.now()->format('Y/m/d').'/result_'.$orderItem->id.'.pdf',
                        'download_count' => fake()->numberBetween(0, 3),
                        'verified_at' => Carbon::parse($order->created_at)->addDays(fake()->numberBetween(1, 7)),
                    ]);
                }

                // Create results for analyzing orders too (partial)
                if ($status === 'analyzing' && fake()->boolean(40)) {
                    $labAssistant = $labAssistants->random();
                    $parameters = $this->generateParameterValues($analysis);

                    MedicalResult::create([
                        'order_item_id' => $orderItem->id,
                        'lab_assistant_id' => $labAssistant->id,
                        'parameter_values' => $parameters,
                        'verified_at' => Carbon::parse($order->created_at)->addDays(fake()->numberBetween(1, 3)),
                    ]);
                }
            }
        }
    }

    /**
     * @return array<int, string>
     */
    private function generateStatusHistory(string $finalStatus): array
    {
        return match ($finalStatus) {
            'completed' => ['new', 'processing', 'ready_for_lab', 'analyzing', 'completed'],
            'cancelled' => fake()->randomElement([
                ['new', 'cancelled'],
                ['new', 'processing', 'cancelled'],
            ]),
            'analyzing' => ['new', 'processing', 'ready_for_lab', 'analyzing'],
            'ready_for_lab' => ['new', 'processing', 'ready_for_lab'],
            'processing' => ['new', 'processing'],
            default => ['new'],
        };
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function generateParameterValues(Analysis $analysis): array
    {
        $ranges = $analysis->reference_ranges;
        if ($ranges === null || ! is_array($ranges)) {
            $param = Str::slug($analysis->name, '_');

            return [
                $param => [
                    'value' => fake()->randomFloat(1, 1, 100),
                    'unit' => 'отн. ед.',
                    'reference' => '—',
                    'out_of_range' => false,
                ],
            ];
        }

        $result = [];
        foreach ($ranges as $key => $range) {
            $min = $range['min'] ?? 0;
            $max = $range['max'] ?? 100;
            $unit = $range['unit'] ?? '';

            // 15% chance of out of range
            $outOfRange = fake()->boolean(15);
            $value = $outOfRange
                ? fake()->randomFloat(1, $max * 1.1, $max * 1.5) // Above range
                : fake()->randomFloat(1, $min * 1.05, $max * 0.95); // Within range

            $result[$key] = [
                'value' => $value,
                'unit' => $unit,
                'reference' => "$min-$max $unit",
                'out_of_range' => $outOfRange,
            ];
        }

        return $result;
    }
}

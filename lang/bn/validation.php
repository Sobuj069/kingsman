<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute অবশ্যই গ্রহণ করতে হবে।',
    'accepted_if' => ':other যখন :value হয়, তখন :attribute অবশ্যই গ্রহণ করতে হবে।',
    'active_url' => ':attribute একটি সঠিক ইউআরএল নয়।',
    'after' => ':attribute অবশ্যই :date এর পরের একটি তারিখ হতে হবে।',
    'after_or_equal' => ':attribute অবশ্যই :date এর পরের অথবা সমান একটি তারিখ হতে হবে।',
    'alpha' => ':attribute শুধুমাত্র বর্ণমালা ধারণ করতে পারে।',
    'alpha_dash' => ':attribute শুধুমাত্র বর্ণমালা, সংখ্যা, ড্যাশ এবং আন্ডারস্কোর ধারণ করতে পারে।',
    'alpha_num' => ':attribute শুধুমাত্র বর্ণমালা এবং সংখ্যা ধারণ করতে পারে।',
    'array' => ':attribute অবশ্যই একটি অ্যারে হতে হবে।',
    'before' => ':attribute অবশ্যই :date এর আগের একটি তারিখ হতে হবে।',
    'before_or_equal' => ':attribute অবশ্যই :date এর আগের অথবা সমান একটি তারিখ হতে হবে।',
    'between' => [
        'array' => ':attribute অবশ্যই :min থেকে :max টি আইটেম থাকতে হবে।',
        'file' => ':attribute অবশ্যই :min থেকে :max কিলোবাইটের মধ্যে হতে হবে।',
        'numeric' => ':attribute অবশ্যই :min থেকে :max এর মধ্যে হতে হবে।',
        'string' => ':attribute অবশ্যই :min থেকে :max টি অক্ষরের মধ্যে হতে হবে।',
    ],
    'boolean' => ':attribute অবশ্যই সত্য অথবা মিথ্যা হতে হবে।',
    'confirmed' => ':attribute কনফার্মেশন মিলছে না।',
    'current_password' => 'পাসওয়ার্ডটি ভুল।',
    'date' => ':attribute একটি সঠিক তারিখ নয়।',
    'date_equals' => ':attribute অবশ্যই :date এর সমান একটি তারিখ হতে হবে।',
    'date_format' => ':attribute অবশ্যই :format ফরম্যাটের সাথে মিলতে হবে।',
    'declined' => ':attribute অবশ্যই প্রত্যাখান করতে হবে।',
    'declined_if' => ':other যখন :value হয়, তখন :attribute অবশ্যই প্রত্যাখান করতে হবে।',
    'different' => ':attribute এবং :other আলাদা হতে হবে।',
    'digits' => ':attribute অবশ্যই :digits অংকের সংখ্যা হতে হবে।',
    'digits_between' => ':attribute অবশ্যই :min থেকে :max অংকের মধ্যে হতে হবে।',
    'dimensions' => ':attribute এর ছবির মাপ সঠিক নয়।',
    'distinct' => ':attribute ফিল্ডে ডুপ্লিকেট মান রয়েছে।',
    'email' => ':attribute অবশ্যই একটি সঠিক ইমেল ঠিকানা হতে হবে।',
    'ends_with' => ':attribute নিচের যেকোনো একটি দিয়ে শেষ হতে হবে: :values',
    'enum' => 'নির্বাচিত :attribute সঠিক নয়।',
    'exists' => 'নির্বাচিত :attribute সঠিক নয়।',
    'file' => ':attribute অবশ্যই একটি ফাইল হতে হবে।',
    'filled' => ':attribute ফিল্ডের মান থাকা আবশ্যক।',
    'gt' => [
        'array' => ':attribute অবশ্যই :value এর বেশি আইটেম থাকতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের বেশি হতে হবে।',
        'numeric' => ':attribute অবশ্যই :value এর বড় হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের বেশি হতে হবে।',
    ],
    'gte' => [
        'array' => ':attribute অবশ্যই :value অথবা তার বেশি আইটেম থাকতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের বেশি অথবা সমান হতে হবে।',
        'numeric' => ':attribute অবশ্যই :value এর বড় অথবা সমান হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের বেশি অথবা সমান হতে হবে।',
    ],
    'image' => ':attribute অবশ্যই একটি ছবি হতে হবে।',
    'in' => 'নির্বাচিত :attribute সঠিক নয়।',
    'in_array' => ':attribute ফিল্ডটি :other এর মধ্যে নেই।',
    'integer' => ':attribute অবশ্যই পূর্ণসংখ্যা হতে হবে।',
    'ip' => ':attribute অবশ্যই একটি সঠিক আইপি ঠিকানা হতে হবে।',
    'ipv4' => ':attribute অবশ্যই একটি সঠিক IPv4 ঠিকানা হতে হবে।',
    'ipv6' => ':attribute অবশ্যই একটি সঠিক IPv6 ঠিকানা হতে হবে।',
    'json' => ':attribute অবশ্যই একটি সঠিক JSON স্ট্রিং হতে হবে।',
    'lt' => [
        'array' => ':attribute অবশ্যই :value এর কম আইটেম থাকতে হবে।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের কম হতে হবে।',
        'numeric' => ':attribute অবশ্যই :value এর ছোট হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের কম হতে হবে।',
    ],
    'lte' => [
        'array' => ':attribute অবশ্যই :value এর বেশি আইটেম থাকতে পারবে না।',
        'file' => ':attribute অবশ্যই :value কিলোবাইটের কম অথবা সমান হতে হবে।',
        'numeric' => ':attribute অবশ্যই :value এর ছোট অথবা সমান হতে হবে।',
        'string' => ':attribute অবশ্যই :value অক্ষরের কম অথবা সমান হতে হবে।',
    ],
    'mac_address' => ':attribute অবশ্যই একটি সঠিক MAC ঠিকানা হতে হবে।',
    'max' => [
        'array' => ':attribute :max এর বেশি আইটেম থাকতে পারবে না।',
        'file' => ':attribute :max কিলোবাইটের বেশি হতে পারবে না।',
        'numeric' => ':attribute :max এর বড় হতে পারবে না।',
        'string' => ':attribute :max অক্ষরের বেশি হতে পারবে না।',
    ],
    'mimes' => ':attribute অবশ্যই :values ধরনের ফাইল হতে হবে।',
    'mimetypes' => ':attribute অবশ্যই :values ধরনের ফাইল হতে হবে।',
    'min' => [
        'array' => ':attribute অবশ্যই কমপক্ষে :min টি আইটেম থাকতে হবে।',
        'file' => ':attribute অবশ্যই কমপক্ষে :min কিলোবাইট হতে হবে।',
        'numeric' => ':attribute অবশ্যই কমপক্ষে :min হতে হবে।',
        'string' => ':attribute অবশ্যই কমপক্ষে :min অক্ষরের হতে হবে।',
    ],
    'multiple_of' => ':attribute অবশ্যই :value এর গুণিতক হতে হবে।',
    'not_in' => 'নির্বাচিত :attribute সঠিক নয়।',
    'not_regex' => ':attribute ফরম্যাটটি সঠিক নয়।',
    'numeric' => ':attribute অবশ্যই সংখ্যা হতে হবে।',
    'password' => [
        'letters' => ':attribute অবশ্যই কমপক্ষে একটি অক্ষর ধারণ করতে হবে।',
        'mixed' => ':attribute অবশ্যই কমপক্ষে একটি বড় অক্ষর এবং একটি ছোট অক্ষর ধারণ করতে করতে হবে।',
        'numbers' => ':attribute অবশ্যই কমপক্ষে একটি সংখ্যা ধারণ করতে হবে।',
        'symbols' => ':attribute অবশ্যই কমপক্ষে একটি প্রতীক ধারণ করতে হবে।',
        'uncompromised' => 'প্রদত্ত :attribute ডেটা লিকে দেখা গেছে। অনুগ্রহ করে একটি নতুন :attribute বেছে নিন।',
    ],
    'present' => ':attribute ফিল্ডটি অবশ্যই উপস্থিত থাকতে হবে।',
    'prohibited' => ':attribute ফিল্ডটি নিষিদ্ধ।',
    'prohibited_if' => ':other যখন :value হয়, তখন :attribute ফিল্ডটি নিষিদ্ধ।',
    'prohibited_unless' => ':other যখন :values এর মধ্যে না হয়, তখন :attribute ফিল্ডটি নিষিদ্ধ।',
    'prohibits' => ':attribute ফিল্ড :other কে উপস্থিত থাকতে বাধা দেয়।',
    'regex' => ':attribute ফরম্যাটটি সঠিক নয়।',
    'required' => ':attribute ফিল্ডটি আবশ্যক।',
    'required_array_keys' => ':attribute অবশ্যই :values এর এন্ট্রিগুলো ধারণ করতে হবে।',
    'required_if' => ':other যখন :value হয়, তখন :attribute আবশ্যক।',
    'required_unless' => ':other যখন :values এর মধ্যে না হয়, তখন :attribute আবশ্যক।',
    'required_with' => ':values যখন উপস্থিত থাকে, তখন :attribute আবশ্যক।',
    'required_with_all' => ':values যখন উপস্থিত থাকে, তখন :attribute আবশ্যক।',
    'required_without' => ':values যখন উপস্থিত না থাকে, তখন :attribute আবশ্যক।',
    'required_without_all' => ':values যখন উপস্থিত না থাকে, তখন :attribute আবশ্যক।',
    'same' => ':attribute এবং :other অবশ্যই এক হতে হবে।',
    'size' => [
        'array' => ':attribute অবশ্যই :size টি আইটেম ধারণ করতে হবে।',
        'file' => ':attribute অবশ্যই :size কিলোবাইট হতে হবে।',
        'numeric' => ':attribute অবশ্যই :size হতে হবে।',
        'string' => ':attribute অবশ্যই :size অক্ষরের হতে হবে।',
    ],
    'starts_with' => ':attribute নিচের যেকোনো একটি দিয়ে শুরু হতে হবে: :values',
    'string' => ':attribute অবশ্যই স্ট্রিং হতে হবে।',
    'timezone' => ':attribute অবশ্যই একটি সঠিক টাইমজোন হতে হবে।',
    'unique' => ':attribute ইতিমধ্যে গ্রহণ করা হয়েছে।',
    'uploaded' => ':attribute আপলোড করতে ব্যর্থ হয়েছে।',
    'url' => ':attribute ফরম্যাটটি সঠিক নয়।',
    'uuid' => ':attribute অবশ্যই একটি সঠিক UUID হতে হবে।',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'নাম',
        'username' => 'ইউজারনেম',
        'email' => 'ইমেল',
        'first_name' => 'নামের প্রথম অংশ',
        'last_name' => 'নামের শেষ অংশ',
        'password' => 'পাসওয়ার্ড',
        'password_confirmation' => 'পাসওয়ার্ড কনফার্মেশন',
        'city' => 'শহর',
        'country' => 'দেশ',
        'address' => 'ঠিকানা',
        'phone' => 'ফোন নম্বর',
        'mobile' => 'মোবাইল',
        'age' => 'বয়স',
        'sex' => 'লিঙ্গ',
        'gender' => 'লিঙ্গ',
        'day' => 'দিন',
        'month' => 'মাস',
        'year' => 'বছর',
        'hour' => 'ঘণ্টা',
        'minute' => 'মিনিট',
        'second' => 'সেকেন্ড',
        'title' => 'শিরোনাম',
        'content' => 'বিষয়বস্তু',
        'description' => 'বিস্তারিত',
        'excerpt' => 'সারসংক্ষেপ',
        'date' => 'তারিখ',
        'time' => 'সময়',
        'available' => 'উপলব্ধ',
        'size' => 'সাইজ',
        'category_id' => 'ক্যাটাগরি',
        'product_name' => 'পণ্যের নাম',
        'price' => 'মূল্য',
        'qty' => 'পরিমাণ',
        'quantity' => 'পরিমাণ',
        'branch_id' => 'ব্রাঞ্চ',
        'supplier_id' => 'সরবরাহকারী',
        'customer_id' => 'ক্রেতা',
        'invoice_no' => 'ইনভয়েস নং',
        'purchase_no' => 'ক্রয় নং',
    ],

];

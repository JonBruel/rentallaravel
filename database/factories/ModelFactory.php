<?php

$factory->define(App\Models\Accountpost::class, function (Faker\Generator $faker) {
    return [
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'customerid' => $faker->randomNumber(),
        'postsource' => $faker->word,
        'amount' => $faker->randomFloat(),
        'currencyid' => $faker->randomNumber(),
        'customercurrencyid' => function () {
             return factory(App\Models\Currency::class)->create()->id;
        },
        'usedrate' => $faker->randomFloat(),
        'text' => $faker->word,
        'contractid' => function () {
             return factory(App\Models\Contract::class)->create()->id;
        },
        'posttypeid' => function () {
             return factory(App\Models\Posttype::class)->create()->id;
        },
        'postedbyid' => $faker->randomNumber(),
        'passifiedby' => $faker->randomNumber(),
        'returndate' => $faker->dateTimeBetween(),
    ];
});

$factory->define(App\Models\Batchfunction::class, function (Faker\Generator $faker) {
    return [
        'batchfunction' => $faker->word,
    ];
});

$factory->define(App\Models\Batchlog::class, function (Faker\Generator $faker) {
    return [
        'statusid' => function () {
             return factory(App\Models\Batchstatus::class)->create()->id;
        },
        'posttypeid' => function () {
             return factory(App\Models\Posttype::class)->create()->id;
        },
        'batchtaskid' => function () {
             return factory(App\Models\Batchtask::class)->create()->id;
        },
        'contractid' => function () {
             return factory(App\Models\Contract::class)->create()->id;
        },
        'accountpostid' => function () {
             return factory(App\Models\Accountpost::class)->create()->id;
        },
        'emailid' => $faker->randomNumber(),
        'customerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'ownerid' => $faker->randomNumber(),
    ];
});

$factory->define(App\Models\Batchstatus::class, function (Faker\Generator $faker) {
    return [
        'status' => $faker->text,
    ];
});

$factory->define(App\Models\Batchtask::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'posttypeid' => $faker->randomNumber(),
        'emailid' => function () {
             return factory(App\Models\Standardemail::class)->create()->id;
        },
        'batchfunctionid' => function () {
             return factory(App\Models\Batchfunction::class)->create()->id;
        },
        'mailto' => $faker->word,
        'paymentbelow' => $faker->randomFloat(),
        'usepaymentbelow' => $faker->boolean,
        'requiredposttypeid' => $faker->randomNumber(),
        'userequiredposttypeid' => $faker->boolean,
        'timedelaystart' => $faker->randomNumber(),
        'usetimedelaystart' => $faker->boolean,
        'timedelayfrom' => $faker->randomNumber(),
        'usetimedelayfrom' => $faker->boolean,
        'addposttypeid' => $faker->randomNumber(),
        'useaddposttypeid' => $faker->boolean,
        'dontfireifposttypeid' => function () {
             return factory(App\Models\Posttype::class)->create()->id;
        },
        'usedontfireifposttypeid' => $faker->boolean,
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'activefrom' => $faker->dateTimeBetween(),
        'active' => $faker->boolean,
    ];
});

$factory->define(App\Models\Bounty::class, function (Faker\Generator $faker) {
    return [
        'version' => $faker->word,
        'userid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'text' => $faker->text,
        'extra' => $faker->word,
    ];
});

$factory->define(App\Models\Bountyanswer::class, function (Faker\Generator $faker) {
    return [
        'bountyid' => function () {
             return factory(App\Models\Bounty::class)->create()->id;
        },
        'userid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'text' => $faker->text,
        'version' => $faker->word,
    ];
});

$factory->define(App\Models\Category::class, function (Faker\Generator $faker) {
    return [
        'category' => $faker->word,
    ];
});

$factory->define(App\Models\Config::class, function (Faker\Generator $faker) {
    return [
        'url' => $faker->url,
        'index' => $faker->text,
        'skin' => $faker->word,
    ];
});

$factory->define(App\Models\Contract::class, function (Faker\Generator $faker) {
    return [
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'customerid' => $faker->randomNumber(),
        'persons' => $faker->randomNumber(),
        'theme' => $faker->text,
        'landingdatetime' => $faker->dateTimeBetween(),
        'departuredatetime' => $faker->dateTimeBetween(),
        'message' => $faker->text,
        'duration' => $faker->randomFloat(),
        'price' => $faker->randomFloat(),
        'discount' => $faker->randomFloat(),
        'finalprice' => $faker->randomFloat(),
        'currencyid' => function () {
             return factory(App\Models\Currency::class)->create()->id;
        },
        'categoryid' => function () {
             return factory(App\Models\Category::class)->create()->id;
        },
        'status' => $faker->word,
    ];
});

$factory->define(App\Models\Contractline::class, function (Faker\Generator $faker) {
    return [
        'periodid' => function () {
             return factory(App\Models\Period::class)->create()->id;
        },
        'contractid' => function () {
             return factory(App\Models\Contract::class)->create()->id;
        },
        'quantity' => $faker->randomFloat(),
    ];
});

$factory->define(App\Models\Culture::class, function (Faker\Generator $faker) {
    return [
        'culture' => $faker->word,
        'culturename' => $faker->word,
        'currencyid' => function () {
             return factory(App\Models\Currency::class)->create()->id;
        },
    ];
});

$factory->define(App\Models\Currency::class, function (Faker\Generator $faker) {
    return [
        'currencyname' => $faker->word,
        'currencysymbol' => $faker->word,
        'rate' => $faker->randomFloat(),
        'listed' => $faker->boolean,
        'code' => $faker->randomNumber(),
    ];
});

$factory->define(App\Models\Currencyrate::class, function (Faker\Generator $faker) {
    return [
        'currencyid' => function () {
             return factory(App\Models\Currency::class)->create()->id;
        },
        'rate' => $faker->randomFloat(),
    ];
});

$factory->define(App\Models\Customer::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'address1' => $faker->word,
        'address2' => $faker->word,
        'address3' => $faker->word,
        'country' => $faker->country,
        'lasturl' => $faker->word,
        'telephone' => $faker->word,
        'mobile' => $faker->word,
        'email' => $faker->safeEmail,
        'login' => $faker->word,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'notes' => $faker->text,
        'customertypeid' => function () {
             return factory(App\Models\Customertype::class)->create()->id;
        },
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'houselicenses' => $faker->randomNumber(),
        'status' => function () {
             return factory(App\Models\Customerstatus::class)->create()->id;
        },
        'cultureid' => function () {
             return factory(App\Models\Culture::class)->create()->id;
        },
    ];
});

$factory->define(App\Models\Customerstatus::class, function (Faker\Generator $faker) {
    return [
        'status' => $faker->word,
    ];
});

$factory->define(App\Models\Customertype::class, function (Faker\Generator $faker) {
    return [
        'customertype' => $faker->text,
    ];
});

$factory->define(App\Models\Emaillog::class, function (Faker\Generator $faker) {
    return [
        'customerid' => $faker->randomNumber(),
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'from' => $faker->word,
        'to' => $faker->word,
        'cc' => $faker->word,
        'text' => $faker->text,
    ];
});

$factory->define(App\Models\House::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'address1' => $faker->word,
        'address2' => $faker->word,
        'address3' => $faker->word,
        'country' => $faker->country,
        'www' => $faker->word,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'lockbatch' => $faker->boolean,
        'currencyid' => function () {
             return factory(App\Models\Currency::class)->create()->id;
        },
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'maidid' => $faker->word,
        'viewfilter' => function () {
             return factory(App\Models\Customertype::class)->create()->id;
        },
        'prepayment' => 0.3333,
        'disttobeach' => $faker->randomNumber(),
        'maxpersons' => $faker->boolean,
        'isprivate' => $faker->boolean,
        'dishwasher' => $faker->boolean,
        'washingmachine' => $faker->boolean,
        'spa' => $faker->boolean,
        'pool' => $faker->boolean,
        'sauna' => $faker->boolean,
        'fireplace' => $faker->boolean,
        'internet' => $faker->boolean,
        'pets' => $faker->boolean,
    ];
});

$factory->define(App\Models\HouseI18n::class, function (Faker\Generator $faker) {
    return [
        'id' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'culture' => $faker->word,
        'description' => $faker->text,
        'shortdescription' => $faker->text,
        'veryshortdescription' => $faker->text,
        'route' => $faker->text,
        'carrental' => $faker->text,
        'conditions' => $faker->text,
        'plan' => $faker->text,
        'gallery' => $faker->text,
        'keywords' => $faker->text,
        'seo' => $faker->text,
        'nature' => $faker->text,
        'sports' => $faker->text,
        'shopping' => $faker->text,
        'environment' => $faker->text,
        'weather' => $faker->text,
    ];
});

$factory->define(App\Models\Menu::class, function (Faker\Generator $faker) {
    return [
        'parentid' => function () {
             return factory(App\Models\Menu::class)->create()->id;
        },
        'description' => $faker->text,
        'path' => $faker->word,
        'customertypes' => $faker->randomNumber(),
        'sortnumber' => $faker->randomNumber(),
    ];
});

$factory->define(App\Models\MenuI18n::class, function (Faker\Generator $faker) {
    return [
        'id' => function () {
             return factory(App\Models\Menu::class)->create()->id;
        },
        'culture' => $faker->word,
        'text' => $faker->word,
    ];
});

$factory->define(App\Models\Period::class, function (Faker\Generator $faker) {
    return [
        'year' => $faker->randomNumber(),
        'weeknumber' => $faker->randomNumber(),
        'enddays' => $faker->word,
        'from' => $faker->dateTimeBetween(),
        'to' => $faker->dateTimeBetween(),
        'theme' => $faker->word,
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'baseprice' => $faker->randomFloat(),
        'basepersons' => $faker->boolean,
        'maxpersons' => $faker->boolean,
        'personprice' => $faker->randomFloat(),
        'extra1' => $faker->word,
        'extra2' => $faker->word,
    ];
});

$factory->define(App\Models\Posttype::class, function (Faker\Generator $faker) {
    return [
        'posttype' => $faker->word,
        'comment' => $faker->word,
        'defaultamount' => $faker->randomNumber(),
    ];
});

$factory->define(App\Models\Right::class, function (Faker\Generator $faker) {
    return [
        'script' => $faker->word,
        'path' => $faker->word,
        'customertypeid' => function () {
             return factory(App\Models\Customertype::class)->create()->id;
        },
        'rights' => $faker->word,
    ];
});

$factory->define(App\Models\Standardemail::class, function (Faker\Generator $faker) {
    return [
        'description' => $faker->word,
        'ownerid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'extra' => $faker->word,
    ];
});

$factory->define(App\Models\StandardemailI18n::class, function (Faker\Generator $faker) {
    return [
        'id' => function () {
             return factory(App\Models\Standardemail::class)->create()->id;
        },
        'culture' => $faker->word,
        'contents' => $faker->text,
    ];
});

$factory->define(App\Models\Testimonial::class, function (Faker\Generator $faker) {
    return [
        'houseid' => function () {
             return factory(App\Models\House::class)->create()->id;
        },
        'userid' => function () {
             return factory(App\Models\Customer::class)->create()->id;
        },
        'text' => $faker->text,
        'extra' => $faker->word,
    ];
});


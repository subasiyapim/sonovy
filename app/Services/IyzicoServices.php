<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Card;
use Iyzipay\Model\CardInformation;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\PaymentChannel;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\Address;
use Iyzipay\Model\Payment;
use Iyzipay\Model\ThreedsInitialize;
use Iyzipay\Model\ThreedsPayment;
use Iyzipay\Request\CreateCardRequest;
use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Request\CreateThreedsPaymentRequest;


class IyzicoServices
{

    private static function APIConfig()
    {
        $options = new Options();

        /*Cache::forget('settings');

        $settings = Cache::rememberForever('settings', function () {
            return Setting::pluck('value', 'key')->all();
        });

        config()->set('settings', $settings);

        $options->setApiKey(config('settings.iyzico_api_key'));
        $options->setSecretKey(config('settings.iyzico_secret_key'));
        $options->setBaseUrl('https://sandbox-api.iyzipay.com');*/

        $integrasion = Integration::where('id', 3)->first();
        $options->setApiKey($integrasion->key);
        $options->setSecretKey($integrasion->secret);
        $options->setBaseUrl($integrasion->url);

        return $options;
    }

    public static function addCreditCard(
        $user_id,
        $user_email,
        $card_holder,
        $card_number,
        $card_exp_year,
        $card_exp_month
    ) {

        $request = new CreateCardRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId('');
        $request->setEmail($user_email);
        $request->setExternalId('');

        $cardInformation = new CardInformation();
        $cardInformation->setCardAlias('C' . $user_id);
        $cardInformation->setCardHolderName($card_holder);
        $cardInformation->setCardNumber($card_number);
        $cardInformation->setExpireMonth($card_exp_month);
        $cardInformation->setExpireYear($card_exp_year);
        $request->setCard($cardInformation);

        $card = Card::create($request, self::APIConfig());

        return $card;
    }

    public static function removeCreditCard($card_token, $card_user_key) {

        $request = new \Iyzipay\Request\DeleteCardRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId('');
        $request->setCardToken($card_token);
        $request->setCardUserKey($card_user_key);

        $card = Card::delete($request, self::APIConfig());

        return $card;
    }

    public static function no3DPayment(
        $price,
        $order_id,
        $user_id,
        $user_email,
        $user_first_name,
        $user_last_name,
        $user_mobile,
        $user_identification_number,
        $user_address,
        $user_country,
        $user_city,
        $user_zipcode,
        $card_holder,
        $card_number,
        $card_exp_year,
        $card_exp_month,
        $card_cvc,
        $item_id,
        $item_name,
        $save_card = 0
    )
    {
        $request = new CreatePaymentRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId('');
        $request->setPrice($price);
        $request->setPaidPrice($price);
        $request->setCurrency(Currency::TL);
        $request->setInstallment(1);
        $request->setBasketId($order_id);
        $request->setPaymentChannel(PaymentChannel::WEB);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);

        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($card_holder);
        $paymentCard->setCardNumber($card_number);
        $paymentCard->setExpireMonth($card_exp_month);
        $paymentCard->setExpireYear($card_exp_year);
        $paymentCard->setCvc($card_cvc);
        $paymentCard->setRegisterCard($save_card);
        $request->setPaymentCard($paymentCard);

        $buyer = new Buyer();
        $buyer->setId("BYR" . $user_id);
        $buyer->setName($user_first_name);
        $buyer->setSurname($user_last_name);
        $buyer->setGsmNumber($user_mobile);
        $buyer->setEmail($user_email);
        $buyer->setIdentityNumber($user_identification_number);
        //$buyer->setLastLoginDate("2015-10-05 12:43:35");
        //$buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($user_address);
        //$buyer->setIp("85.34.78.112");
        $buyer->setCity($user_city);
        $buyer->setCountry($user_country);
        $buyer->setZipCode($user_zipcode);
        $request->setBuyer($buyer);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($user_first_name . ' ' . $user_last_name);
        $shippingAddress->setCity($user_city);
        $shippingAddress->setCountry($user_country);
        $shippingAddress->setAddress($user_address);
        $shippingAddress->setZipCode($user_zipcode);
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new Address();
        $billingAddress->setContactName($user_first_name . ' ' . $user_last_name);
        $billingAddress->setCity($user_city);
        $billingAddress->setCountry($user_country);
        $billingAddress->setAddress($user_address);
        $billingAddress->setZipCode($user_zipcode);
        $request->setBillingAddress($billingAddress);

        $basketItems = array();
        $firstBasketItem = new BasketItem();
        $firstBasketItem->setId($item_id);
        $firstBasketItem->setName($item_name);
        //$firstBasketItem->setCategory1("Collectibles");
        //$firstBasketItem->setCategory2("Accessories");
        //$firstBasketItem->setItemType(BasketItemType::PHYSICAL);
        $firstBasketItem->setPrice($price);
        $basketItems[0] = $firstBasketItem;
        $request->setBasketItems($basketItems);

        $payment = Payment::create($request, self::APIConfig());

        return $payment;
    }

    public static function init3DPayment(
        $price,
        $conversation_id,
        $order_id,
        $user_id,
        $user_email,
        $user_first_name,
        $user_last_name,
        $user_mobile,
        $user_identification_number,
        $user_address,
        $user_country,
        $user_city,
        $user_zipcode,
        $card_holder,
        $card_number,
        $card_exp_year,
        $card_exp_month,
        $card_cvc,
        $item_id,
        $item_name,
        $item_category,
        $save_card = 0
    )
    {
        $request = new CreatePaymentRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId($conversation_id);
        $request->setPrice($price);
        $request->setPaidPrice($price);
        $request->setCurrency(Currency::TL);
        $request->setInstallment(1);
        $request->setBasketId($order_id);
        $request->setPaymentChannel(PaymentChannel::WEB);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);
        $request->setCallbackUrl(route('iyzico-callback'));

        $paymentCard = new PaymentCard();
        $paymentCard->setCardHolderName($card_holder);
        $paymentCard->setCardNumber($card_number);
        $paymentCard->setExpireMonth($card_exp_month);
        $paymentCard->setExpireYear($card_exp_year);
        $paymentCard->setCvc($card_cvc);
        $paymentCard->setRegisterCard($save_card);
        $request->setPaymentCard($paymentCard);

        $buyer = new Buyer();
        $buyer->setId("BYR" . $user_id);
        $buyer->setName($user_first_name);
        $buyer->setSurname($user_last_name);
        $buyer->setGsmNumber($user_mobile);
        $buyer->setEmail($user_email);
        $buyer->setIdentityNumber($user_identification_number);
        //$buyer->setLastLoginDate("2015-10-05 12:43:35");
        //$buyer->setRegistrationDate("2013-04-21 15:12:09");
        $buyer->setRegistrationAddress($user_address);
        //$buyer->setIp("85.34.78.112");
        $buyer->setCity($user_city);
        $buyer->setCountry($user_country);
        $buyer->setZipCode($user_zipcode);
        $request->setBuyer($buyer);

        $shippingAddress = new Address();
        $shippingAddress->setContactName($user_first_name . ' ' . $user_last_name);
        $shippingAddress->setCity($user_city);
        $shippingAddress->setCountry($user_country);
        $shippingAddress->setAddress($user_address);
        $shippingAddress->setZipCode($user_zipcode);
        $request->setShippingAddress($shippingAddress);

        $billingAddress = new Address();
        $billingAddress->setContactName($user_first_name . ' ' . $user_last_name);
        $billingAddress->setCity($user_city);
        $billingAddress->setCountry($user_country);
        $billingAddress->setAddress($user_address);
        $billingAddress->setZipCode($user_zipcode);
        $request->setBillingAddress($billingAddress);

        $basketItems = array();
        $firstBasketItem = new BasketItem();
        $firstBasketItem->setId($item_id);
        $firstBasketItem->setName($item_name);
        $firstBasketItem->setCategory1($item_category);
        //$firstBasketItem->setCategory2("Accessories");
        $firstBasketItem->setItemType(BasketItemType::VIRTUAL);
        $firstBasketItem->setPrice($price);
        $basketItems[0] = $firstBasketItem;
        $request->setBasketItems($basketItems);

        $payment = ThreedsInitialize::create($request, self::APIConfig());

        return $payment;
    }

    public static function confirm3DPayment($payment_id, $conversation_id)
    {
        $request = new CreateThreedsPaymentRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId($conversation_id);
        $request->setPaymentId($payment_id);
        //$request->setConversationData("conversation data");

        $threedsPayment = ThreedsPayment::create($request, self::APIConfig());

        return $threedsPayment;
    }

}

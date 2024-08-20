<?php

use App\Http\Controllers\{
    AdminController,
    AdvertController,
    BankController,
    BrandController,
    BuyerController,
    BuyerPayoutController,
    BuyerReferralController,
    BuyerReferralProgramController,
    CartController,
    CountryController,
    CouponController,
    DiscountController,
    HomeDeliveryController,
    JourneyController,
    LogisticsPersonnelController,
    OrderController,
    OrderJourneyController,
    PaystackPaymentController,
    ProductCategoryController,
    ProductController,
    ProductRatingController,
    ProductSubCategoryController,
    SpecificationController,
    StateController,
    StoreController,
    StorePayoutController,
    TagController,
    TownController,
    UserController,
    VendorController,
    WalletFundingController
};
use App\Http\Controllers\Auth\Login\{PasswordLoginController};
use App\Http\Controllers\Auth\{LogoutController, PasswordController, RegistrationController};
use App\Http\Resources\Users\{AdminResource, BuyerResource, VendorResource, LogisticsPersonnelResource};
use App\Models\Users\{Admin, Buyer, Vendor};
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::get('/', function (Request $request) {
        $user = Auth::user();

        return $user instanceof Admin ?
            AdminResource::make($user) : ($user instanceof Buyer ? BuyerResource::make($user) : ($user instanceof Vendor ?
                VendorResource::make($user)
                :
                LogisticsPersonnelResource::make($user)
            )
            );
    });
    Route::put('/', [UserController::class, 'update']);
});

// CONCERNING AUTHENTICATION
Route::prefix('auth')->group(function () {
    Route::prefix('/login')->group(function () {
        Route::post('/', [PasswordLoginController::class, 'generalAuthenticate'])->name('login');
        Route::prefix('/admin')->group(function () {
            Route::post('/', [PasswordLoginController::class, 'adminAuthenticate']);
        });
        Route::prefix('/buyer')->group(function () {
            Route::post('/', [PasswordLoginController::class, 'buyerAuthenticate']);
        });
        Route::prefix('/vendor')->group(function () {
            Route::post('/', [PasswordLoginController::class, 'vendorAuthenticate']);
        });
    });
    Route::prefix('/register')->group(function () {
        Route::prefix('/admin')->group(function () {
            Route::post('/', [RegistrationController::class, 'registerAdmin'])->middleware(['auth:admin']);
        });
        Route::prefix('/buyer')->group(function () {
            Route::post('/', [RegistrationController::class, 'registerBuyer']);
        });
        Route::prefix('/logistics-personnel')->group(function () {
            Route::post('/', [RegistrationController::class, 'registerLogisticsPersonnel'])->middleware(['auth:admin']);
        });
        Route::prefix('/vendor')->group(function () {
            Route::post('/', [RegistrationController::class, 'registerVendor']);
        });
    });
    Route::post('/logout', LogoutController::class);


    Route::prefix('/email')->group(function () {
        Route::get('/verify', function () {
            return view('auth.verify-email');
        })->middleware('auth:sanctum')->name('verification.notice');

        Route::get('/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return response(['message' => 'Email Verified']);
        })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');

        Route::post('/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return response(['message' => 'Email verification message sent.']);
        })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
    });

    Route::prefix('/password')->group(function () {
        Route::post('/forgot', [PasswordController::class, 'forgot'])->middleware('guest');
        Route::post('/reset', [PasswordController::class, 'reset'])->middleware('guest');
    });
});

// PRODUCT CATEGORIES
Route::prefix('product-categories')->group(function () {
    Route::get('/', [ProductCategoryController::class, 'index']);
    Route::get('/{productCategory}', [ProductCategoryController::class, 'show']);
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::post('/', [ProductCategoryController::class, 'create']);
        Route::put('/{productCategory}', [ProductCategoryController::class, 'update']);
        Route::delete('/{productCategory}', [ProductCategoryController::class, 'delete']);
        Route::delete('/force-delete/{productCategory}', [ProductCategoryController::class, 'forceDelete']);
    });
});

// PRODUCT SUB-CATEGORIES
Route::prefix('product-sub-categories')->group(function () {
    Route::get('/', [ProductSubCategoryController::class, 'index']);
    Route::get('/{productSubCategory}', [ProductSubCategoryController::class, 'show']);
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::post('/', [ProductSubCategoryController::class, 'create']);
        Route::put('/{productSubCategory}', [ProductSubCategoryController::class, 'update']);
        Route::delete('/{productSubCategory}', [ProductSubCategoryController::class, 'delete']);
        Route::delete('/force-delete/{productSubCategory}', [ProductSubCategoryController::class, 'forceDelete']);
    });
});

// PRODUCTS
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::middleware(['auth:sanctum', 'auth:vendor'])->group(function () {
        Route::post('/', [ProductController::class, 'create']);
        Route::put('/{product}', [ProductController::class, 'update']);
    });
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::delete('/{product}', [ProductController::class, 'delete']);
        Route::delete('/force-delete/{product}', [ProductController::class, 'forceDelete']);
    });

    // PRODUCT REVIEWS
    Route::prefix('/{product}/ratings')->group(function () {
        Route::get('/', [ProductRatingController::class, 'index']);
        Route::get('/{rating}', [ProductRatingController::class, 'show']);
        Route::post('/', [ProductRatingController::class, 'create'])->middleware(['auth:buyer']);
        Route::delete('/{rating}', [ProductRatingController::class, 'delete'])->middleware(['auth:admin']);
        Route::delete('/force-delete/{rating}', [ProductRatingController::class, 'forceDelete'])->middleware(['auth:admin']);
    });
});

// BRANDS
Route::prefix('brands')->group(function () {
    Route::get('/', [BrandController::class, 'index']);
    Route::get('/{brand}', [BrandController::class, 'show']);
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::post('/', [BrandController::class, 'create']);
        Route::put('/{brand}', [BrandController::class, 'update']);
        Route::delete('/{brand}', [BrandController::class, 'delete']);
        Route::delete('/force-delete/{brand}', [BrandController::class, 'forceDelete']);
    });
});

// Tags
Route::prefix('tags')->group(function () {
    Route::get('/', [TagController::class, 'index']);
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::get('/{tag}', [TagController::class, 'show']);
        Route::post('/', [TagController::class, 'create']);
        Route::put('/{tag}', [TagController::class, 'update']);
        Route::delete('/{tag}', [TagController::class, 'delete']);
        Route::delete('/force-delete/{tag}', [TagController::class, 'forceDelete']);
    });
});

// Tags
Route::prefix('specifications')->group(function () {
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::get('/', [SpecificationController::class, 'index']);
        Route::get('/{specification}', [SpecificationController::class, 'show']);
        Route::post('/', [SpecificationController::class, 'create']);
        Route::put('/{specification}', [SpecificationController::class, 'update']);
        Route::delete('/{specification}', [SpecificationController::class, 'delete']);
        Route::delete('/force-delete/{specification}', [SpecificationController::class, 'forceDelete']);
    });
});

// CONCERNING THE CART
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('view-cart');
    Route::post('/add', [CartController::class, 'add'])->name('add-to-cart');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove-from-cart');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear-cart');
});

// CONCERNING ORDERS
Route::prefix('orders')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout')->middleware(['auth:buyer']);
    Route::delete('/{order}', [OrderController::class, 'destroy'])->name('order.delete')->middleware(['auth:buyer']);
    Route::post('/update-shipping', [OrderController::class, 'updateShipping'])->name('update-shipping')->middleware(['auth:buyer']);

    Route::prefix('{order}')->group(function () {
        Route::get('/', [OrderController::class, 'show'])->name('order.show')->middleware(['auth:buyer,admin']);
        Route::post('/initiate-waybill', [OrderJourneyController::class, 'create'])->name('order.init-waybill')->middleware(['auth:admin']);
        Route::post('/initiate-home-delivery', [HomeDeliveryController::class, 'create'])->name('order.init-home-delivery')->middleware(['auth:buyer']);
        Route::post('/disable-home-delivery', [HomeDeliveryController::class, 'disable'])->name('order.disable-home-delivery')->middleware(['auth:buyer']);

        Route::prefix('pay')->group(function () {
            Route::post('/paystack', [OrderController::class, 'pay'])->name('order.pay.paystack')->middleware(['auth:buyer']);
            Route::post('/wallet', [OrderController::class, 'payFromWallet'])->name('order.pay.wallet')->middleware(['auth:buyer']);
        });

        Route::prefix('payments')->group(function () {
            Route::get('/', [OrderController::class, 'showPayments'])->middleware(['auth:admin, buyer, vendor']);
            Route::get('/paystack', [OrderController::class, 'showPaystackPayments'])->middleware(['auth:admin, buyer, vendor']);
            Route::get('/wallet', [OrderController::class, 'showWalletPayments'])->middleware(['auth:admin, buyer, vendor']);
        });
    });
});

// Concerning Adverts
Route::prefix('adverts')->group(function () {
    Route::get('/', [AdvertController::class, 'index']);
    Route::get('/{advert}', [AdvertController::class, 'show']);
    Route::middleware(['auth:sanctum', 'auth:admin'])->group(function () {
        Route::post('/', [AdvertController::class, 'create']);
        Route::put('/{advert}', [AdvertController::class, 'update']);
        Route::delete('/{advert}', [AdvertController::class, 'delete']);
    });
});

// Concerning Discounts
Route::prefix('discounts')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [DiscountController::class, 'index'])->middleware(['auth:admin,vendor']);
    Route::get('/{discount}', [DiscountController::class, 'show'])->middleware(['auth:admin,vendor']);
    Route::middleware(['auth:vendor'])->group(function () {
        Route::post('/', [DiscountController::class, 'create']);
        Route::put('/{discount}', [DiscountController::class, 'update']);
        Route::delete('/{discount}', [DiscountController::class, 'delete']);
    });
});

// Concerning Countries
Route::prefix('countries')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CountryController::class, 'index']);
    Route::get('/{country}', [CountryController::class, 'show']);
});

// Concerning Countries
Route::prefix('states')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [StateController::class, 'index']);
    Route::get('/{state}', [StateController::class, 'show']);
});

// Concerning Towns
Route::prefix('towns')->group(function () {
    Route::get('/', [TownController::class, 'index']);
    Route::get('/{town}', [TownController::class, 'show']);
    Route::middleware(['auth:admin'])->group(function () {
        Route::post('/', [TownController::class, 'create']);
        Route::put('/{town}', [TownController::class, 'update']);
        Route::delete('/{town}', [TownController::class, 'delete']);
    });
});

// Concerning Banks
Route::prefix('banks')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [BankController::class, 'index']);
    Route::get('/{bank}', [BankController::class, 'show'])->where('bank', '^(?!paystack).*$');
    Route::get('/paystack', [BankController::class, 'paystackList']);
});

// CONCERNING PAYMENTS
Route::prefix('payments')->group(function () {
    Route::prefix('/paystack')->group(function () {
        Route::webhooks('/webhook', 'paystack');
        Route::post('/pay', [PaystackPaymentController::class, 'redirectToGateway'])->name('pay');
        Route::get('/callback', [PaystackPaymentController::class, 'handleGatewayCallback']);
    });
});

Route::prefix('wallets')->group(function () {
    Route::get('/funding-list', [WalletFundingController::class, 'index'])->middleware('auth:admin,buyer');
    Route::prefix('/wallet')->middleware(['auth:buyer'])->group(function () {
        Route::post('/fund', [WalletFundingController::class, 'fund'])->name('fund');
    });
});

// Concerning Coupons
Route::prefix('coupons')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CouponController::class, 'index'])->middleware('auth:admin,vendor');
    Route::get('/{coupon}', [CouponController::class, 'show']);
    Route::post('/', [CouponController::class, 'create'])->middleware('auth:admin,vendor');
    Route::put('/{coupon}', [CouponController::class, 'update'])->middleware('auth:admin,vendor');
    Route::delete('/{coupon}', [CouponController::class, 'delete'])->middleware('auth:admin');
});

// Concerning KYC
Route::prefix('kyc')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/bvn')->group(function () {
        Route::post('/upload', [UserController::class, 'uploadBvn'])->middleware(['auth:buyer,vendor']);
    });
});

// Concerning Users
Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::prefix('admins')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->middleware('auth:admin');
        Route::get('/{admin}', [AdminController::class, 'show'])->middleware('auth:admin');
    });
    Route::prefix('buyers')->group(function () {
        Route::get('/', [BuyerController::class, 'index'])->middleware('auth:admin');
        Route::get('/{buyer}', [BuyerController::class, 'show'])->middleware('auth:admin');
    });
    Route::prefix('vendors')->group(function () {
        Route::get('/', [VendorController::class, 'index'])->middleware('auth:admin');
        Route::get('/{vendor}', [VendorController::class, 'show'])->middleware('auth:admin');
    });
    Route::prefix('logistics-personnels')->group(function () {
        Route::get('/', [LogisticsPersonnelController::class, 'index'])->middleware('auth:admin');
        Route::get('/{logisticsPersonnel}', [LogisticsPersonnelController::class, 'show'])->middleware('auth:admin');
    });
});

Route::prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::get('/{store}', [StoreController::class, 'show'])->where('store', '[0-9]+');
    Route::get('/{store}/products', [StoreController::class, 'listProducts'])->where('store', '[0-9]+');
    Route::get('/{store}/finance', [StoreController::class, 'finance'])->middleware('auth:admin')->where('store', '[0-9]+');
    Route::get('/products', [StoreController::class, 'listVendorProducts'])->middleware('auth:vendor');
    Route::get('/finance', [StoreController::class, 'storeFinance'])->middleware('auth:vendor');
});

// Concerning Payouts
Route::prefix('store-payouts')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [StorePayoutController::class, 'index'])->middleware('auth:admin,vendor');
    Route::get('/{storePayout}', [StorePayoutController::class, 'show'])->middleware('auth:admin,vendor');
    Route::post('/', [StorePayoutController::class, 'create'])->middleware('auth:vendor');
    Route::delete('/{storePayout}', [StorePayoutController::class, 'destroy'])->middleware('auth:admin');
    Route::post('/{storePayout}/approve', [StorePayoutController::class, 'approve'])->middleware('auth:admin');
    Route::post('/{storePayout}/decline', [StorePayoutController::class, 'decline'])->middleware('auth:admin');
});

Route::prefix('buyer-payouts')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [BuyerPayoutController::class, 'index'])->middleware('auth:admin,buyer');
    Route::get('/{buyerPayout}', [BuyerPayoutController::class, 'show'])->middleware('auth:admin,buyer');
    Route::post('/', [BuyerPayoutController::class, 'create'])->middleware('auth:buyer');
    Route::delete('/{buyerPayout}', [BuyerPayoutController::class, 'destroy'])->middleware('auth:admin');
    Route::post('/{buyerPayout}/approve', [BuyerPayoutController::class, 'approve'])->middleware('auth:admin');
    Route::post('/{buyerPayout}/decline', [BuyerPayoutController::class, 'decline'])->middleware('auth:admin');
});

// Concerning Referrals
Route::prefix('referrals')->middleware('auth:sanctum')->group(function () {
    Route::prefix('buyer-referral-programs')->group(function () {
        Route::get('/', [BuyerReferralProgramController::class, 'index'])->middleware('auth:admin,buyer');
        Route::get('/{buyerReferralProgram}', [BuyerReferralProgramController::class, 'show'])->middleware('auth:admin,buyer');
        Route::post('/', [BuyerReferralProgramController::class, 'create'])->middleware('auth:buyer');
        Route::post('/{buyerReferralProgram}/activate', [BuyerReferralProgramController::class, 'activate'])->middleware('auth:admin');
        Route::post('/{buyerReferralProgram}/deactivate', [BuyerReferralProgramController::class, 'deactivate'])->middleware('auth:admin');
    });

    Route::prefix('buyer-referrals')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [BuyerReferralController::class, 'index'])->middleware('auth:admin,buyer');
        Route::get('/{buyerReferral}', [BuyerReferralController::class, 'show'])->middleware('auth:admin,buyer');
    });
});

// Concerning Shipments
Route::prefix('journeys')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [JourneyController::class, 'index'])->middleware('auth:admin,logistics_personnel');
    Route::get('/{journey}', [JourneyController::class, 'show']);
    Route::post('/', [JourneyController::class, 'create'])->middleware('auth:admin');
    Route::put('/{journey}', [JourneyController::class, 'update'])->middleware('auth:admin,logistics_personnel');
    Route::delete('/{journey}', [JourneyController::class, 'delete'])->middleware('auth:admin');

    Route::prefix('/{journey}')->group(function () {
        Route::post('/assign-logistics-personnel', [JourneyController::class, 'assignLogisticsPersonnel'])->middleware('auth:admin');
        Route::middleware('auth:logistics_personnel')->group(function () {
            Route::post('/set-origin', [JourneyController::class, 'setOrigin']);
            Route::post('/set-destination', [JourneyController::class, 'setDestination']);
            Route::post('/set-checkpoint', [JourneyController::class, 'setCheckpoint']);
            Route::post('/mark-as-left', [JourneyController::class, 'markAsLeft']);
            Route::post('/mark-as-arrived', [JourneyController::class, 'markAsArrived']);
        });
    });
});

// Concerning Way-bills
Route::prefix('order-journeys')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [OrderJourneyController::class, 'index'])->middleware('auth:admin,logistics_personnel,buyer');
    Route::get('/{orderJourney}', [OrderJourneyController::class, 'show'])->middleware('auth:admin,logistics_personnel,buyer');
    // Route::delete('/{order-journey}', [OrderJourneyController::class, 'delete'])->middleware('auth:admin');

    Route::prefix('/{orderJourney}')->group(function () {
        Route::post('/assign-to-journey', [OrderJourneyController::class, 'assignToJourney'])->middleware('auth:admin');
        Route::post('/mark-as-received', [OrderJourneyController::class, 'markAsReceived'])->middleware('auth:buyer');
        Route::middleware('auth:logistics_personnel')->group(function () {
            Route::post('/mark-as-delivered', [OrderJourneyController::class, 'markAsDelivered']);
        });
    });
});

// Concerning Home deliveries
Route::prefix('home-deliveries')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [HomeDeliveryController::class, 'index'])->middleware('auth:admin,logistics_personnel');
    Route::get('/{homeDelivery}', [HomeDeliveryController::class, 'show']);
    // Route::delete('/{order-journey}', [HomeDeliveryController::class, 'delete'])->middleware('auth:admin');

    Route::prefix('/{homeDelivery}')->group(function () {
        Route::post('/assign-logistics-personnel', [HomeDeliveryController::class, 'assignLogisticsPersonnel'])->middleware('auth:admin');
        Route::post('/update-journey', [HomeDeliveryController::class, 'updateJourney'])->middleware('auth:admin');
        Route::post('/mark-as-received', [HomeDeliveryController::class, 'markAsReceived'])->middleware('auth:buyer');
        Route::middleware('auth:logistics_personnel')->group(function () {
            Route::post('/set-origin', [HomeDeliveryController::class, 'setOrigin']);
            Route::post('/set-destination', [HomeDeliveryController::class, 'setDestination']);
            Route::post('/mark-as-left', [HomeDeliveryController::class, 'markAsLeft']);
            Route::post('/mark-as-arrived', [HomeDeliveryController::class, 'markAsArrived']);
            Route::post('/mark-as-delivered', [HomeDeliveryController::class, 'markAsDelivered']);
        });
    });
});

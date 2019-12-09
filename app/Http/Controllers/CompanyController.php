<?php

namespace App\Http\Controllers;

use Hash;
use Carbon\Carbon;

use App\User;
use App\Image;
use App\Offer;

class CompanyController extends Controller
{
    public function index()
    {
        $loggedUser = request()->user();

        $offers = $loggedUser
            ->offers()
            ->latest()
            ->with(['user:id,name', 'startDestinations', 'endDestinations'])
            ->get();

        return layout('Offer')
            ->with('title', 'Offer')
            ->with('color', 'blue')
            ->with('header', region('OfferHeader'))
            ->with(
                'content',
                collect()
                    ->push(
                        component('Title')
                            ->is('large')
                            ->is('white')
                            ->with('title', trans('company.index.title'))
                    )
                    ->push(region('OfferAdminButtons', $loggedUser))
                    ->br()
                    ->merge(
                        $offers->map(function ($offer) {
                            return component('OfferRow')
                                ->is($offer->status ? '' : 'unpublished')
                                ->with('offer', $offer);
                        })
                    )
            )
            ->with('footer', region('FooterLight', ''))
            ->render();
    }

    public function adminIndex()
    {
        $companies = User::whereCompany(true)->get();

        $offers = Offer::latest()
            ->with(['user:id,name', 'startDestinations', 'endDestinations'])
            ->take(100)
            ->get();

        return layout('Offer')
            ->with('head_robots', 'noindex')
            ->with('title', 'Offer')
            ->with('color', 'blue')
            ->with('header', region('OfferHeader'))
            ->with(
                'content',
                collect()
                    ->push(
                        component('Title')
                            ->is('large')
                            ->is('white')
                            ->with('title', trans('company.index.title'))
                    )
                    ->push(
                        component('Button')
                            ->is('orange')
                            ->is('narrow')
                            ->with('title', trans('company.index.create'))
                            ->with('route', route('company.create'))
                    )
                    ->merge(
                        $companies->map(function ($company) {
                            return component('Flex')
                                ->with('align', 'center')
                                ->with(
                                    'items',
                                    collect()
                                        ->push(
                                            component('Title')
                                                ->is('small')
                                                ->is('white')
                                                ->with('title', $company->name)
                                                ->with('route', route('company.show', $company))
                                        )
                                        ->push(
                                            component('Button')
                                                ->is('orange')
                                                ->is('small')
                                                ->is('narrow')
                                                ->with('title', trans('company.index.edit'))
                                                ->with(
                                                    'route',
                                                    route('company.edit', [
                                                        $company,
                                                        'redirect' => 'company.admin.index'
                                                    ])
                                                )
                                        )
                                );
                        })
                    )
                    ->br()
                    ->push(
                        component('Title')
                            ->is('large')
                            ->is('white')
                            ->with('title', trans('company.index.offer'))
                    )
                    ->merge(
                        $offers->map(function ($offer) {
                            return component('OfferRow')
                                ->is($offer->status == 1 ? '' : 'unpublished')
                                ->with('offer', $offer)
                                ->with('route', $offer->status == 1 ? route('offer.show', [$offer]) : '');
                        })
                    )
            )
            ->with('footer', region('FooterLight', ''))
            ->render();
    }

    public function show($id)
    {
        $user = User::whereCompany(true)->findOrFail($id);
        return redirect()->route('offer.index', ['user_id' => $user->id]);
    }

    public function create()
    {
        $loggedUser = request()->user();

        return layout('Offer')
            ->with('title', 'Offer')
            ->with('color', 'blue')
            ->with('header', region('OfferHeader'))
            ->with(
                'content',
                collect()->push(
                    component('Title')
                        ->is('large')
                        ->is('white')
                        ->is('center')
                        ->with('title', trans('company.create.title'))
                )
            )
            ->with(
                'bottom',
                collect()->push(
                    component('Form')
                        ->with('route', route('company.store'))
                        ->with('files', true)
                        ->with(
                            'fields',
                            collect()
                                ->push(
                                    component('Title')
                                        ->is('small')
                                        ->is('blue')
                                        ->with('title', trans('company.edit.credentials'))
                                )
                                ->push(
                                    component('FormTextfield')
                                        ->is('large')
                                        ->with('title', trans('company.edit.name.title'))
                                        ->with('name', 'name')
                                        ->with('value', old('name'))
                                )
                                ->push(
                                    component('FormTextfield')
                                        ->is('large')
                                        ->with('title', trans('company.edit.company_name.title'))
                                        ->with('name', 'company_name')
                                        ->with('value', old('company_name'))
                                )

                                ->push(
                                    component('FormPassword')
                                        ->is('large')
                                        ->with('title', trans('company.edit.password.title'))
                                        ->with('name', 'password')
                                        ->with('value', '')
                                )
                                ->push(
                                    component('FormPassword')
                                        ->is('large')
                                        ->with('title', trans('company.edit.password_confirmation.title'))
                                        ->with('name', 'password_confirmation')
                                        ->with('value', '')
                                )
                                ->push(
                                    component('Title')
                                        ->is('small')
                                        ->is('blue')
                                        ->with('title', trans('company.edit.about'))
                                )
                                ->push(component('FormUpload')->with('name', 'file'))
                                ->push(
                                    component('FormTextarea')
                                        ->with('rows', 4)
                                        ->with('title', trans('company.edit.description.title'))
                                        ->with('name', 'description')
                                        ->with('value', old('description'))
                                )
                                ->push(
                                    component('Title')
                                        ->is('small')
                                        ->is('blue')
                                        ->with('title', trans('company.edit.contacts.title'))
                                )
                                ->push(
                                    component('FormTextfield')
                                        ->is('large')
                                        ->with('title', trans('company.edit.email.title'))
                                        ->with('name', 'email')
                                        ->with('value', old('email'))
                                )
                                ->push(
                                    component('FormTextfield')
                                        ->with('title', trans('company.edit.homepage.title'))
                                        ->with('name', 'contact_homepage')
                                        ->with('value', old('contact_homepage'))
                                )
                                ->push(
                                    component('FormTextfield')
                                        ->with('title', trans('company.edit.facebook.title'))
                                        ->with('name', 'contact_facebook')
                                        ->with('value', old('contact_facebook'))
                                )
                                ->push(
                                    component('FormTextfield')
                                        ->with('title', trans('company.edit.instagram.title'))
                                        ->with('name', 'contact_instagram')
                                        ->with('value', old('contact_instagram'))
                                )
                                ->push(
                                    component('FormButton')
                                        ->is('wide')
                                        ->is('large')
                                        ->is('orange')
                                        ->with('title', trans('company.create.submit'))
                                )
                        )
                )
            )
            ->with('footer', region('FooterLight', ''))
            ->render();
    }

    public function store()
    {
        $maxfilesize = config('site.maxfilesize') * 1024;

        $rules = [
            'name' => 'required|unique:users,name',
            'company_name' => 'required|unique:users,real_name',
            'email' => 'required|unique:users,email',
            'password' => 'required|sometimes|confirmed|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'description' => 'min:2',
            'contact_facebook' => 'url',
            'contact_twitter' => 'url',
            'contact_instagram' => 'url',
            'contact_homepage' => 'url',
            'file' => "image|max:$maxfilesize"
        ];

        $this->validate(request(), $rules);

        $user = User::create([
            'name' => request()->name,
            'email' => request()->email,
            'password' => Hash::make(request()->password),
            'real_name' => request()->company_name,
            'real_name_show' => 1,
            'notify_message' => 0,
            'notify_follow' => 0,
            'description' => request()->description,
            'contact_facebook' => request()->contact_facebook,
            'contact_instagram' => request()->contact_instagram,
            'contact_twitter' => '',
            'contact_homepage' => request()->contact_homepage,
            'active_at' => Carbon::now(),
            'verified' => 1,
            'company' => true
        ]);

        if (request()->hasFile('file')) {
            $filename =
                'picture-' .
                $user->id .
                '.' .
                request()
                    ->file('file')
                    ->getClientOriginalExtension();

            $filename = Image::storeImageFile(request()->file('file'), $filename);

            $user->images()->delete();
            $user->images()->create(['filename' => $filename]);
        }

        return redirect()
            ->route('company.index')
            ->with('info', trans('company.create.info'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return layout('Offer')
            ->with('title', 'Offer')
            ->with('color', 'blue')
            ->with('header', region('OfferHeader'))
            ->with(
                'content',
                collect()->push(
                    component('Title')
                        ->is('large')
                        ->is('white')
                        ->is('center')
                        ->with('title', trans('company.edit.title'))
                )
            )
            ->with(
                'bottom',
                collect()->push(
                    component('Form')
                        ->with('route', route('company.update', [$id]))
                        ->with('method', 'PUT')
                        ->with('files', true)
                        ->with(
                            'fields',
                            collect()
                                // ->push(
                                //     component('Title')
                                //         ->is('small')
                                //         ->is('blue')
                                //         ->with('title', trans('company.edit.credentials'))
                                // )
                                ->pushWhen(
                                    request()->has('redirect'),
                                    component('FormHidden')
                                        ->with('name', 'redirect')
                                        ->with('value', request()->redirect)
                                )
                                ->push(
                                    component('FormButton')
                                        ->is('wide')
                                        ->is('large')
                                        ->is('orange')
                                        ->with('title', trans('company.edit.submit'))
                                )
                        )
                )
            )
            ->render();
    }

    public function update($id)
    {
        return redirect()
            ->route(request()->has('redirect') ? request()->redirect : 'company.index')
            ->with('info', trans('company.edit.info'));
    }
}
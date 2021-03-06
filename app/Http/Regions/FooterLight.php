<?php

namespace App\Http\Regions;

use Carbon\Carbon;

class FooterLight
{
    protected function prepareCol1Links()
    {
        $user = request()->user();

        return collect()
            ->push([
                'title' => trans('menu.footer.flights'),
                'route' => route('flight.index')
            ])
            ->push([
                'title' => trans('menu.footer.travelmates'),
                'route' => route('travelmate.index')
            ])
            ->push([
                'title' => trans('menu.footer.news'),
                'route' => route('news.index')
            ])
            // @LAUNCH Remove this check
            ->pushWhen($user && $user->hasRole('superuser'), [
                'title' => trans('menu.footer.offer'),
                'route' => route('offer.index')
            ])
            ->push([
                'title' => trans('menu.footer.blogs'),
                'route' => route('blog.index')
            ])
            ->push([
                'title' => trans('menu.footer.photos'),
                'route' => route('photo.index')
            ])
            ->push([
                'title' => trans('menu.footer.destinations'),
                'route' => route('destination.index')
            ])
            ->map(function ($item) {
                return (object) $item;
            });
    }

    protected function prepareCol2Links()
    {
        return collect()
            ->push([
                'title' => trans('menu.footer2.forum'),
                'route' => route('forum.index')
            ])
            ->push([
                'title' => trans('menu.footer2.buysell'),
                'route' => route('buysell.index')
            ])
            ->push([
                'title' => trans('menu.footer2.expat'),
                'route' => route('expat.index')
            ])
            ->map(function ($item) {
                return (object) $item;
            });
    }

    protected function prepareCol3Links()
    {
        $loggedUser = request()->user();

        return collect()
            ->push([
                'title' => trans('menu.footer3.about'),
                'route' => route('static.show', 'tripist')
            ])
            ->push([
                'title' => trans('menu.footer3.contact'),
                'route' => route('static.show', 'kontakt')
            ])
            ->push([
                'title' => trans('menu.footer3.eula'),
                'route' => route('static.show', 'kasutustingimused')
            ])
            ->push([
                'title' => trans('menu.footer3.privacy'),
                'route' => route('static.show', 'privaatsustingimused')
            ])
            ->push([
                'title' => trans('menu.footer3.advertising'),
                'route' => route('static.show', 'reklaam')
            ])
            ->pushWhen(!$loggedUser, [
                'title' => trans('menu.auth.login'),
                'route' => route('login.form')
            ])
            ->pushWhen(!$loggedUser, [
                'title' => trans('menu.auth.register'),
                'route' => route('register.form')
            ])
            ->pushWhen($loggedUser, [
                'title' => trans('menu.auth.logout'),
                'route' => route('login.logout')
            ])
            ->map(function ($item) {
                return (object) $item;
            });
    }

    protected function prepareSocialLinks()
    {
        return collect()
            ->push([
                'title' => trans('menu.footer-social.facebook'),
                'route' => 'https://facebook.com/tripeeee',
                'icon' => component('Icon')
                    ->is('white')
                    ->with('icon', 'icon-facebook'),
                'target' => '_blank'
            ])
            ->push([
                'title' => trans('menu.footer-social.twitter'),
                'route' => 'https://twitter.com/trip_ee',
                'icon' => component('Icon')
                    ->is('white')
                    ->with('icon', 'icon-twitter'),
                'target' => '_blank'
            ])
            ->push([
                'title' => trans('menu.footer-social.flightfeed'),
                'route' => '/lendude_sooduspakkumised/rss',
                'icon' => component('Icon')
                    ->is('white')
                    ->with('icon', 'icon-rss'),
                'target' => ''
            ])
            ->push([
                'title' => trans('menu.footer-social.newsfeed'),
                'route' => '/index.atom',
                'icon' => component('Icon')
                    ->is('white')
                    ->with('icon', 'icon-rss'),
                'target' => ''
            ])
            ->map(function ($item) {
                return (object) $item;
            });
    }

    public function render($color = 'light')
    {
        return component('Footer')
            ->is($color)
            ->with('logo_route', route('frontpage.index'))
            ->with(
                'logo',
                component('Icon')
                    ->is('darkGray')
                    ->with('icon', 'tripee_logo_plain')
                    ->with('width', '100')
                    ->with('height', '25')
                    ->with('color', 'white')
            )
            ->with('links', [
                'col1' => $this->prepareCol1Links(),
                'col2' => $this->prepareCol2Links(),
                'col3' => $this->prepareCol3Links(),
                'social' => $this->prepareSocialLinks()
            ])
            ->with(
                'licence',
                trans('site.footer.copyright', [
                    'current_year' => Carbon::now()->year
                ])
            );
    }
}

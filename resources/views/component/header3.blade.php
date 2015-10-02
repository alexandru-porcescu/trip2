@if (isset($__env->getSections()['header3.left']) || isset($__env->getSections()['header3.left']) || isset($__env->getSections()['header3.left']))

<div class="component-header-3">

    <div class="container">

        <div class="row">
        
            <div class="
                left
                col-sm-4
            ">                
                @yield('header3.left')

            </div>

            <div class="
                center
                col-sm-4
                utils-padding-left
            ">

                @yield('header3.center')

            </div>
            
            <div class="
                right
                col-sm-4
                utils-padding-left
            ">
                
                @yield('header3.right')

            </div>

        </div>
    
    </div>

</div>

@endif
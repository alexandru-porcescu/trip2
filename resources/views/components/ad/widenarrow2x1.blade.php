<div class="row">
    
    <div class="col-sm-8">
        
        @include('components.ad.ad',[
            'title' => 'Sample wide ad',
            'height' => 3
        ])
        
    </div>

    <div class="col-sm-4">
        
        @include('components.ad.ad',[
            'title' => 'Sample narrow ad',
            'height' => 5
        ])
        
    </div>

</div>
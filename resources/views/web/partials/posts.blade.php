@if(count($posts)==0)
       <div class="alert alert-warning" role="alert">No quedan más entradas.</div>
@else
    @foreach($posts as $post)
        <div class="post-items card col-md-12 no-pd">
            <div class="media col-md-4 no-pd">
                <a href="{{route('blog_get_index').'/'.$post->link}}">
                    <figure>
                        <img class="media-object img-responsive" width="100%"  src="{{$post->image}}" alt="{{$post->title}}">
                    </figure>
                </a>
            </div>
            <div class="col-md-8 pd-t-15 pd-b-15">

                <h3 class="list-group-item-heading blogTitle"><a class="blogIndexLink" href="{{route('blog_get_index').'/'.$post->link}}">{{$post->title}}</a></h3>

                <p class="list-group-item-text"> {{$post->summary}}</p>
                <br>
                <div class="row blogIndexFooter">
                    <span class="pull-left pd-l-15"><small>Publicado por {{$post->author}}</small><br><small>{{ $post->created_at->diffForHumans()}}</small></span>
                    <br class="pull-right">
                    <a type="button" class="btn btn-link shareBlog pull-right no-pd" rel="popover"><i class="fas fa-share-alt" aria-hidden="true"></i> Compartir</a>
                    <div class="share-popover-content">
                        <a class="facebook fb-share-button" data-href="{{route('blog_get_index').'/'.$post->link}}" href="https://www.facebook.com/sharer/sharer.php?u={{route('blog_get_index').'/'.$post->link}}&amp;src=sdkpreparse" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a class="twitter mg-l-20" href="https://twitter.com/intent/tweet?text={{$post->title}}&via=transporter_es&url={{route('blog_get_index').'/'.$post->link}}" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="https://www.linkedin.com/shareArticle?mini=false&url={{route('blog_get_index').'/'.$post->link}}&title={{$post->title}}&summary={{$post->summary}}&source={{$post->image}}" class="linkedin mg-l-20" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif

 <header>
            @if(Route::has('login'))
                <nav>
                    @auth
                        
                        <form action="/logout" method="POST">
                            @csrf
                            <button type="submit" class="btn bg-gradient-primary" >Cerrar sesion</button>
                        </form>   
                    @else
                        <a href="{{route('login')}}">Log in</a>
                        <a href="{{route('register')}}">Registro</a>
             
                    @endauth
                </nav>
            @endif
</header>
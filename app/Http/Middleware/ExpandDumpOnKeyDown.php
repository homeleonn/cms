<?php

namespace App\Http\Middleware;

use Closure;

class ExpandDumpOnKeyDown
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
		$content = $response->getContent();
		ob_start();
		?>
		<script>
			function $$(callback){window.addEventListener('load', callback);}
			
			$$(() => {
				let a = $$$();
				// a();
				document.addEventListener('keydown', function(event) {
					if (event.code == 'KeyX' && (event.altKey)) {
						a();
					}
				});
			});
			
			function $$$(){
				var list = document.getElementsByTagName('samp');
				
				return function () {
					// console.log(list[0].children);
					for (var i = 0; i < list.length; i++) {
						var children = list[i].children;
						for (var j = 0; j < children.length; j++) {
							if (children[j].className == 'sf-dump-ref sf-dump-toggle' || children[j].className == 'sf-dump-compact') {
								children[j].click();
							}
						}
					}
				}
				
			}
		</script>
		<?php
		$data = ob_get_clean();
		$response->setContent($content . $data);
        return $response;
    }
}

function wp_zinc_autocomplete_setup(){wpzinc_autocompleters=[],wpzinc_autocomplete.forEach((function(t,e){var n=[];t.triggers.forEach((function(t,e){t.selectTemplate=function(t){return t.original.value},"url"in t&&(t.values=function(e,n){data=new FormData,data.append("action",t.action),data.append("nonce",t.nonce),data.append("search",e),fetch(t.url,{method:t.method,credentials:"same-origin",body:data}).then((function(t){return t.json()})).then((function(t){n(t.data)})).catch((function(t){console.error(t)}))}),n.push(t)}));var c=new Tribute({collection:n});wpzinc_autocompleters.push({fields:t.fields,instance:c})}))}function wp_zinc_autocomplete_initialize(){wpzinc_autocompleters.forEach((function(t,e){t.fields.forEach((function(e,n){t.instance.attach(document.querySelectorAll(e))}))}))}function wp_zinc_autocomplete_destroy(){wpzinc_autocompleters.forEach((function(t,e){t.fields.forEach((function(e,n){t.instance.detach(document.querySelectorAll(e))}))})),wpzinc_autocompleters=[]}var wpzinc_autocompleters=[];wp_zinc_autocomplete_setup(),wp_zinc_autocomplete_initialize();
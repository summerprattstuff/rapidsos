!function(e){e(".media-categories-module-modal").on("click",(function(t){t.preventDefault();var i=e(this).data("content-view"),a=e(this).data("sidebar-view"),n=e(this).data("title"),r=e(this).data("button-label"),d=e(this).data("output"),l=e(this).data("model");MediaLibraryOrganizerModalWindow.content(new MediaLibraryOrganizerViewContainer({contentView:i,sidebarView:a,title:n,buttonLabel:r,model:l})),MediaLibraryOrganizerModalWindow.open(),mediaLibraryOrganizerSelectizeInit()}))}(jQuery);var MediaLibraryOrganizerModalWindow=new wp.media.view.Modal({controller:{trigger:function(){}}}),MediaLibraryOrganizerViewContainer=wp.Backbone.View.extend({tagName:"div",className:"media-frame mode-select wp-core-ui hide-router hide-menu",template:wp.template("media-categories-module-content-view"),events:{"keyup input":"updateItem","keyup textarea":"updateItem","change input":"updateItem","change textarea":"updateItem","blur textarea":"updateItem","change select":"updateItem","click button.media-button-insert":"insert"},initialize:function(e){this.contentView=e.contentView,this.sidebarView=e.sidebarView,this.title=e.title,this.buttonLabel=e.buttonLabel,this.model=new Backbone.Model(e.model)},render:function(){return this.$el.html(this.template({title:this.title,buttonLabel:this.buttonLabel})),this.$el.find("div.media-content").append(wp.media.template(this.contentView)),this.$el.find("div.media-sidebar").append(wp.media.template(this.sidebarView)),this},updateItem:function(e){if(""!=e.target.name){switch(e.target.type){case"checkbox":value=e.target.checked?e.target.value:0;break;default:value=jQuery(e.target).val();break}this.model.set(e.target.name,value)}},insert:function(){MediaLibraryOrganizerModalWindow.close()}}),MediaLibraryOrganizerSidebarViewContainer=wp.Backbone.View.extend({tagName:"div",className:"sidebar",template:wp.template("media-categories-module-sidebar-view"),initialize:function(e){this.view=e.view},render:function(){return this.$el.html(wp.template(this.args.view)),this}});
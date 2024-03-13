# SASS - How To

#### This is small tutorial what do files and folders represent

### Folder structure
```
    sass/
    |__base/
    |   |__ _grid.scss
    |   |__ _print.scss
    |   |__ _reboot.scss
    |   |__ _root.scss
    |   |__ _type.scss
    |   |.. ...
    |
    |__components/
    |   |__post/
    |   |   |__ _post-content.scss
    |   |__site/
    |   |   |__ _section-header.scss
    |
    |__mixins/
    |   |__ _box_shadow.scss
    |   |__ _breakpoints.scss
    |   |__ _caret.scss
    |   |__ _clearfix.scss
    |   |__ _display.scss
    |   |.. ...
    |
    |__utils/
    |   |__ _functions.scss
    |   |.. ...
    |
    |_vars/
    |   |__ _colors.scss
    |   |__ _components.scss
    |   |__ _grid.scss
    |   |__ _options.scss
    |   |__ _type.scss
    |   |.. ...
    |
    |__vendor/
    |   |__fontawesome/
    |   |   |__ _font-awesome.scss
    |   |   |.. ...
    |   |__slick/
    |   |   |__ _slick.scss
    |   |   |__ _slick-theme.scss
    |   |   |.. ...
    |   |--acf/
    |   |   |__ acf.scss
    |   |.. ...
    |
    |__woocommerce/
    |   |__ woo.scss
    |
    |__sandbox/
    |   |.. ...
    |__ _base.scss
    |__ _mixins.scss
    |__ _vars.scss
    |__ _vendor.scss
    |__ style.scss
```

- **Base** should include global styles settings, grid, print styles, typography etc...
- **Mixins** should have all mixins inside (for creating grid, custom helpful mixins etc...)
- **Components** should have all reusable components across the site
- **Utils** utility folder - typically only functions and some helpful sass mixins used for testing/developing and less in project
- **Vars** all variables across site (splitted in as much subfolders as makes sense)
- **Vendor** contain all SCSS files from external libraries and frameworks. (ACF styles will be put in special file (not style.css))
- **Sandbox** not to be used directly in production, it is place to test/storage some scss that will/might be useful
- **Woocommerce** NOT FOR PRODUCTION - Styles for woocommerce that are yet to be implemented and tested (imported from new _underscore theme) 
- *NOTE* Feel free to add more folders/files depending on project needs
### Formatting

#### Indentation and spacing
- Be consistent with indentation. On Wordpress projects, indentation is 1 tab
- Use a single space after a property's name colon
- Use one whitespace between an element and a bracket
- Use one selector per line, one rule per line
```scss
    .button,
    .link {
        color: $white;
        text-decoration: none;
    }
 ```
- Use Line-Break between styles, new element and after closing the bracket
```scss
    .button {
        background-color: $red;

        &.button-large {
            width: 100%;
        }

        &:hover {
            background-color: $black;
        }

        @media #{$md-down} {
            font-size: 0.8em;
        }
    }

    .link {
        color: $blue;
    }
```
- No space before and after single line comment that wraps styles section
```scss
    // Button - START
    .button {
        background-color: $black;
    }
    // Button - END
```
- Comments that are in the same line as code are perceded by one space
```scss
    .button-move {
        cursor: pointer; // Use as falback
        cursor: grab;
    }
```
- Commas in function arguments should be followed by a single space
```scss
    .button {
        box-shadow: 0 1px 1px 2px rgba($black, .2);
        transition: all 2.5s cubic-bezier(1, 0, 0, 1);
    }
```
### Naming
- Use hyphens rather than underscores and camelCase in class names
```scss
    // Bad example
    .button_outline {
        ...
    }

    .buttonOutline {
        ...
    }

    // Good example
    .button-outline {
        ...
    }
```
- Use hyphens rather than underscores and camelCase in variable and mixin names
- Do not use ID selectors
- Use a tag as a root selector only in base declaration partial
- Use meaningful or generic class names
- Use class names that are as short as possible but as long as necessary
```scss
    // Not recommended
    .navigation {}
    .athr {}

    // Recommended
    .nav {}
    .author {}
```
### Nesting and Ordering
- Selectors should be nested in the following way: pseudo-elements, pseudo-selectors, @inlcude blocks, child selectors. Order can be changed if certain child selectors are affected by parent hover, for example.
```scss
    .button {
        @include hide-text;
        width: 100px;
        color: $white;
        background-color: $facebook;

        &::before {
            ...
        }

        &::after {
            ...
        }

        &:hover {
            ...
        }

        &:focus {
            ...
        }

        @media #{$md-up} {
            width: 100%;
        }

        .icon-facebook {
            font-size: 0.9em;
        }
    }
```
- Nesting depth should not be greater than 5
- Selector depth should not be greater than 3
### Style Rules
- Do not use units after 0 values unless they are required
```scss
    // Bad example
    .button {
        box-shadow: 0px 0px 1px $black;
        margin: 0px;
    }

    // Good example
    .button {
        box-shadow: 0 0 1px $black;
        margin: 0;
    }
```
- Always use hex notation for color variables
```scss
    // Bad example
    $black: black;
    $white: rgb(255, 255, 255);
    $red: hsl(0, 100%, 50%);

    // Good example
    $black: #000;
    $white: #fff;
    $red: #f00;
```
- Use color variables as function arguments
```scss
    background-color: rgba($black, .2);
    color: lighten($black, 80%);
```
- Always use single quotes
```scss
    &::before {
        content: '';
    }
```
- Never use vendor prefixes

### Comments
- Always use single line comments style
```scss
    .button {
        @inlcude hide-text; // Text for icon button needs to be hidden
    }

    // This
    // is
    // the
    // multiline
    // comment
```
- Use 'temp style' comment in uppercase with your name alongside
```scss
    .button {
        color: $red !important; // TEMP - John Doe
    }
```

### Grid
- There are helpful grid mixins that you should use across the site (old ones from previous theme version will be included just for comparing)
- Grid variables (media and wrapper width) is located in *_vars/_grid.scss*
- Grid classes are as $grid-breakpoints names + adding _ in front of each breakpoint like this (example with 12 columns):
```
._12 (12 columns width (width: 100%) on all screens)
._6 (6 columns width (width: 50%) on all screens)

._xs12 (12 columns width (width 100%) after 480px device width)
._xs6 (6 columns width (width 50%) after 480px device width)

._s12 (12 columns width (width 100%) after 576device width)
._s6 (6 columns width (width 50%) after 576px device width)
---
```
- Adding ._*_auto is (ex: ._xs_autowhere * is nothing or breakpoint) will make flex: none (or flex: 0 0 auto)
- .ord_*number will add order to that column
- .ord_*_first(last) will set column to first or last place
- .ofs_*number will set offset to column

```
$grid-breakpoints: (
        _: 0,
        xs: 480px,
        s: 576px,
        m: 768px,
        l: 992px,
        xl: 1200px
) !default;

// Previously @media #{$md-up}
@include media-up(s) {
    ... // Target media above 576px width
}
// Previously @media #{$md-down}
@include media-down(l) {
    ... // Target media under 992px width
}
// Previously @media #{$md-only}
@include media-only(xs) {
    ... // Target media between 480px and 575.98px width
}
// Didn't have previous mixin or shortcut
@include media-between(s, xl) {
    ... // Target media between 576px and 1199.98px width
}
// Previously @media #{$landscape}
@include media-landscape {
    ... // Target mobile devices with landscape orientation
        // min-width: $mobile-landscape-width (located at /_vars/_grid.scss)
        // max-height: $mobile-landscape-height (located at /_vars/_grid.scss)
}
// Previously @media #{$laptop}
@include media-laptop {
    ... // Target screen devices with narrow height but big width (laptops with reduced screen sizes usually)
        // min-width: $narrow-laptop-width (located at /_vars/_grid.scss)
        // max-height: $narrow-laptop-height (located at /_vars/_grid.scss)
}
```
#### For any questions, ask Bojan Krsmanovic
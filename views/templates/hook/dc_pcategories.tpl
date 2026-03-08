{**
 * Design Cart pCategories – szablon frontowy
 *}
<section id="dc-pcategories" class="dc-pcategories" style="background-color:{$dc_module_bg|escape:'html':'UTF-8'}; padding: 2rem 0;">
  <div class="container">
    {if $dc_intro_title || $dc_intro_desc}
      <div class="dc-pcategories-intro" style="margin-bottom: 1.5rem; width: 100%; text-align: {if $dc_intro_align == 'center'}center{else}left{/if};">
        {if $dc_intro_title}
          <h2 class="dc-pcategories-intro-title" style="font-size: {$dc_intro_title_size|intval}px; color: {$dc_intro_title_color|escape:'html':'UTF-8'}; margin-bottom: 0.5rem;">
            {$dc_intro_title|escape:'html':'UTF-8'}
          </h2>
        {/if}
        {if $dc_intro_desc}
          <div class="dc-pcategories-intro-desc" style="font-size: {$dc_intro_desc_size|intval}px; color: {$dc_intro_desc_color|escape:'html':'UTF-8'};">
            {$dc_intro_desc|nl2br|escape:'html':'UTF-8'}
          </div>
        {/if}
      </div>
    {/if}

    <div class="dc-pcategories-grid" style="display: flex; flex-wrap: wrap; gap: 1.5rem; margin: 0 -0.5rem;">
      {foreach from=$dc_categories item=cat}
        <div class="dc-pcategories-tile-wrap" style="flex: 0 0 {$dc_tile_flex_basis|escape:'html':'UTF-8'}; min-width: 0; padding: 0 0.5rem; margin-bottom: 0; box-sizing: border-box;">
          <a href="{$cat.url|escape:'html':'UTF-8'}" class="dc-pcategories-tile" style="display: flex; flex-direction: column; align-items: {if $dc_cat_align == 'center'}center{else}flex-start{/if}; background-color: {$dc_tile_bg|escape:'html':'UTF-8'}; border-radius: 8px; overflow: hidden; text-decoration: none; color: inherit; height: 100%; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: box-shadow 0.2s, transform 0.2s; padding: 0; min-height: 120px; box-sizing: border-box;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.12)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.08)'; this.style.transform='translateY(0)';">
            {if $dc_show_image && $cat.image}
              <div class="dc-pcategories-tile-img" style="width: {$dc_image_width|intval}%; {if $dc_cat_align == 'center'}margin: 0 auto;{else}margin: 0;{/if} padding-top: 0.75rem; padding-left: 1rem; padding-right: 1rem; flex-shrink: 0;">
                <img src="{$cat.image|escape:'html':'UTF-8'}" alt="{$cat.name|escape:'html':'UTF-8'}" loading="lazy" style="width: 100%; height: auto; display: block; object-fit: contain;">
              </div>
            {/if}
            <div class="dc-pcategories-tile-name" style="width: 100%; padding: 1rem; font-size: {$dc_cat_name_size|intval}px; color: {$dc_cat_name_color|escape:'html':'UTF-8'}; font-weight: 600; text-align: {if $dc_cat_align == 'center'}center{else}left{/if}; flex-shrink: 0;">
              {$cat.name|escape:'html':'UTF-8'}
            </div>
          </a>
        </div>
      {/foreach}
    </div>
  </div>
</section>

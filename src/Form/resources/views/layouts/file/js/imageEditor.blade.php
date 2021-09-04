@if (in_array($element->getAttributes('data-image-mode'), ['3', 'editor']))

    <div class="grid-x grid-padding-x align-center hide" id="editor_menu_{{ $element->id }}">
        <div class="cell shrink">
            <div class="button-group">
                <button class="button" id="editor_undo_{{ $element->id }}">@icon('icon-undo')</button>
                <button class="button" id="editor_redo_{{ $element->id }}">@icon('icon-redo')</button>
            </div>
        </div>
        <div class="cell shrink">
            <div class="button-group">
                <button class="button" id="editor_h_{{ $element->id }}">@icon('icon-arrows-alt-h')</button>
                <button class="button" id="editor_v_{{ $element->id }}">@icon('icon-arrows-alt-v')</button>
            </div>
        </div>
        <div class="cell shrink">
            <div class="button-group">
                <button class="button" id="editor_sync_{{ $element->id }}">@icon('icon-sync')</button>
                <button class="button" id="editor_check_{{ $element->id }}">@icon('icon-check')</button>
            </div>
        </div>
    </div>

@endif

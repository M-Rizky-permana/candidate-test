<div>
    <x-input-label for="layer_order" value="Layer Order" />
    <x-text-input id="layer_order" name="layer_order" type="number" min="1" class="mt-1 block w-full" :value="old('layer_order', $layer->layer_order ?? '')" required />
    <x-input-error class="mt-2" :messages="$errors->get('layer_order')" />
</div>

<div class="grid gap-4 md:grid-cols-3">
    <div>
        <x-input-label for="thickness" value="Thickness" />
        <x-text-input id="thickness" name="thickness" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('thickness', $layer->thickness ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('thickness')" />
    </div>
    <div>
        <x-input-label for="width" value="Width" />
        <x-text-input id="width" name="width" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('width', $layer->width ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('width')" />
    </div>
    <div>
        <x-input-label for="angle" value="Angle" />
        <x-text-input id="angle" name="angle" type="number" step="0.01" min="-360" max="360" class="mt-1 block w-full" :value="old('angle', $layer->angle ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('angle')" />
    </div>
</div>

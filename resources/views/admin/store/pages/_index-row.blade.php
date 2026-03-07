<tr class="@if($page->parent)child-page-row @endif">
    <td class="cell-name">
        <a href="{{ route('admin.store.pages.edit', [$page->store, $page->id]) }}" title="Edit Page">{{ $page->name }}</a>
    </td>
    <td class="cell-slug">{{$page->slug}}</th>
    <td>{{$page->parent->name ?? ''}}</th>
    <td>
        <img src="{{ $page->background_image_url }}" style="max-width:120px;">
    </td>
    <td class="col-narrow col-center">
        <a href="{{ route('admin.store.pages.hotspots', [$page->store, $page->id]) }}" title="Edit Hotspots">
            {{ $page->hotspots()->count() }}<br />
        </a>
    </td>
    <td class="col-narrow col-center">{{ $page->html_content ? 'Yes' : 'No' }}</td>

    <td class="col-narrow col-center">{{ $page->hide_from_menu ? 'Yes' : 'No' }}</td>

    <td class="col-narrow col-center">{{ $page->menu_display_order }}</td>

    <td class="text-right button-cell">
        <a class="btn btn-primary btn-sm" href="{{ route('admin.store.pages.edit', [$page->store, $page->id]) }}">
            Edit
        </a>

        <form class="inline-form" action="{{ route('admin.store.pages.delete', [$page->store, $page->id]) }}" method="POST">
            @method('delete')
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" dusk="page-{{ $page->id }}-delete"
                onclick="return confirm('Are you sure?')">
                Delete
            </button>
        </form>
    </td>
</tr>
{{-- recurse child pages --}}
@each('admin.store.pages._index-row', $page->children, 'page')
 
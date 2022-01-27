<div class="table-scroll" data-sortable data-is-sortable="{{ $items->isSortableRows }}">
    <table class="admin-table sortable unstriped">

        @foreach ($items as $item)

            @if ($loop->first)

                <thead>
                <tr>

                    @if ($items->getActions()->checkbox && $item->getActions()->checkbox)

                        <th>

                            {{ $items->getActions()->checkbox->render() }}

                        </th>

                    @endif

                    @if ($items->getActions()->action || $item->getActions()->action)

                        <th>

                            @if ($items->getActions()->action)

                                {{ $items->getActions()->action->render() }}

                            @endif

                        </th>

                    @endif

                    @if ($items->getColumns()->isNotEmpty())

                        @foreach ($items->getColumns() as $column => $params)

                            <th {!! $params->attributes ?? '' !!}>
                                <nobr>

                                    @if ($params->link)

                                        <a href="{{ $params->link }}">

                                            @endif

                                            {!! $params->name !!}

                                            @if ($params->checked)

                                                @icon('icon icon-sort-amount-' . ($params->desc ? 'down' : 'up'))

                                            @endif

                                            @if ($params->link)

                                        </a>

                                    @endif

                                </nobr>

                                @if ($params->text)

                                    <div><small><nobr>{{ $params->text }}</nobr></small></div>

                                @endif

                            </th>

                        @endforeach

                    @endif

                </tr>
                </thead>
                <tbody>

            @endif

                <tr{!! $items->isSortableRows && !$item->getSorting() ? ' class="ui-sortable-disabled"' : '' !!}>

                    @if ($items->getActions()->checkbox && $item->getActions()->checkbox)

                        <td>

                            {{ $item->getActions()->checkbox->render() }}

                        </td>

                    @endif

                    @if ($items->getActions()->action || $item->getActions()->action)

                        <td>

                            @if ($item->getActions()->action)

                                {{ $item->getActions()->action->render() }}

                            @endif

                        </td>

                    @endif

                    @if ($items->getColumns()->isNotEmpty())

                        @foreach ($items->getColumns() as $column => $params)

                            <td>

                                @if ($params->length > 0 && $item->getCroppedRenderText($params['column'], $params->length, true))

                                    @include('admin::layouts.table.text')

                                @else

                                    {!! $item->getRenderer($params['column']) ?: '&mdash;' !!}

                                @endif

                            </td>

                        @endforeach

                    @endif

                </tr>

                @endforeach

                </tbody>
    </table>
</div>

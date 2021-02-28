<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class TicketSearch extends Component
{
    use WithPagination;

    public $user;
    public $perPage = 25;
    public $searchTerm = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function paginationView()
    {
        return 'vendor.pagination.livewire-pagination';
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->user = \auth()->user();
    }

    public function getTicketsProperty()
    {
        if ($this->user->group->is_modo) {
            return Ticket::query()
                ->with(['user', 'category', 'priority'])
                ->when($this->searchTerm, function ($query) {
                    return $query->where('subject', 'LIKE', '%'.$this->searchTerm.'%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            return Ticket::query()
                ->with(['user', 'category', 'priority'])
                ->where('user_id', '=', $this->user->id)
                ->when($this->searchTerm, function ($query) {
                    return $query->where('subject', 'LIKE', '%'.$this->searchTerm.'%');
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate($this->perPage);
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        return \view('livewire.ticket-search', [
            'tickets' => $this->tickets,
        ]);
    }
}

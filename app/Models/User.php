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

namespace App\Models;

use App\Helpers\Bbcode;
use App\Helpers\Linkify;
use App\Helpers\StringHelper;
use App\Traits\UsersOnlineTrait;
use Carbon\Carbon;
use Gstt\Achievements\Achiever;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use voku\helper\AntiXSS;

/**
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $passkey
 * @property int $group_id
 * @property int $active
 * @property int $uploaded
 * @property int $downloaded
 * @property string|null $image
 * @property string|null $title
 * @property string|null $about
 * @property string|null $signature
 * @property int $fl_tokens
 * @property float $seedbonus
 * @property int $invites
 * @property int $hitandruns
 * @property string $rsskey
 * @property int $chatroom_id
 * @property int $censor
 * @property int $chat_hidden
 * @property int $hidden
 * @property int $style
 * @property int $nav
 * @property int $torrent_layout
 * @property int $torrent_filters
 * @property string|null $custom_css
 * @property int $ratings
 * @property int $read_rules
 * @property int $can_chat
 * @property int $can_comment
 * @property int $can_download
 * @property int $can_request
 * @property int $can_invite
 * @property int $can_upload
 * @property int $show_poster
 * @property int $peer_hidden
 * @property int $private_profile
 * @property int $block_notifications
 * @property int $stat_hidden
 * @property int $twostep
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $last_login
 * @property string|null $disabled_at
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $locale
 * @property int $chat_status_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TorrentRequest[] $ApprovedRequests
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TorrentRequest[] $FilledRequests
 * @property-read \Illuminate\Database\Eloquent\Collection|\Gstt\Achievements\Model\AchievementProgress[] $achievements
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Article[] $articles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserAudible[] $audibles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BonTransactions[] $bonGiven
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BonTransactions[] $bonReceived
 * @property-read \App\Models\Torrent $bookmarks
 * @property-read \App\Models\ChatStatus $chatStatus
 * @property-read \App\Models\Chatroom $chatroom
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserEcho[] $echoes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FeaturedTorrent[] $featuredTorrent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Follow[] $follows
 * @property-read string $slug
 * @property-read \App\Models\Group $group
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\History[] $history
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Torrent[] $moderated
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Note[] $notes
 * @property-read \App\Models\UserNotification $notification
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Peer[] $peers
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessage[] $pm_receiver
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PrivateMessage[] $pm_sender
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Poll[] $polls
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read \App\Models\UserPrivacy $privacy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $receivedInvite
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $reports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TorrentRequestBounty[] $requestBounty
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TorrentRequest[] $requests
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rss[] $rss
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invite[] $sentInvite
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Report[] $solvedReports
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ban[] $staffban
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Warning[] $staffdeletedwarning
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Warning[] $staffwarning
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription[] $subscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thank[] $thanksGiven
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Thank[] $thanksReceived
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Topic[] $topics
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Torrent[] $torrents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ban[] $userban
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Warning[] $userwarning
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Wish[] $wishes
 *
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereBlockNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCanChat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCanComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCanDownload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCanInvite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCanRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCanUpload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCensor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereChatHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereChatStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereChatroomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCustomCss($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDisabledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereFlTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereHitandruns($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInvites($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLastLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereNav($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePasskey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePeerHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePrivateProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRatings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereReadRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRsskey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSeedbonus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereShowPoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStatHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTorrentFilters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTorrentLayout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereTwostep($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use Achiever;
    use SoftDeletes;
    use UsersOnlineTrait;

    /**
     * The Attributes Excluded From The Model's JSON Form.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'passkey',
        'rsskey',
        'remember_token',
        'api_token',
    ];

    /**
     * The Attributes That Should Be Mutated To Dates.
     *
     * @var array
     */
    protected $dates = [
        'last_login',
        'last_action',
    ];

    /**
     * Belongs To A Group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class)->withDefault([
            'color'         => config('user.group.defaults.color'),
            'effect'        => config('user.group.defaults.effect'),
            'icon'          => config('user.group.defaults.icon'),
            'name'          => config('user.group.defaults.name'),
            'slug'          => config('user.group.defaults.slug'),
            'position'      => config('user.group.defaults.position'),
            'is_admin'      => config('user.group.defaults.is_admin'),
            'is_freeleech'  => config('user.group.defaults.is_freeleech'),
            'is_immune'     => config('user.group.defaults.is_immune'),
            'is_incognito'  => config('user.group.defaults.is_incognito'),
            'is_internal'   => config('user.group.defaults.is_internal'),
            'is_modo'       => config('user.group.defaults.is_modo'),
            'is_trusted'    => config('user.group.defaults.is_trusted'),
            'can_upload'    => config('user.group.defaults.can_upload'),
            'level'         => config('user.group.defaults.level'),
        ]);
    }

    /**
     * Belongs To A Chatroom.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatroom()
    {
        return $this->belongsTo(Chatroom::class);
    }

    /**
     * Belongs To A Chat Status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chatStatus()
    {
        return $this->belongsTo(ChatStatus::class, 'chat_status_id', 'id');
    }

    /**
     * Belongs To Many Bookmarks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarks()
    {
        return $this->belongsToMany(Torrent::class, 'bookmarks', 'user_id', 'torrent_id')->withTimeStamps();
    }

    public function isBookmarked($torrent_id)
    {
        return $this->bookmarks()->where('torrent_id', '=', $torrent_id)->first() !== null;
    }

    /**
     * Has Many Messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Has One Privacy Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function privacy()
    {
        return $this->hasOne(UserPrivacy::class);
    }

    /**
     * Has One Chat Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function chat()
    {
        return $this->hasOne(UserChat::class);
    }

    /**
     * Has One Notifications Object.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function notification()
    {
        return $this->hasOne(UserNotification::class);
    }

    /**
     * Has Many RSS Feeds.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rss()
    {
        return $this->hasMany(Rss::class);
    }

    /**
     * Has Many Echo Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function echoes()
    {
        return $this->hasMany(UserEcho::class);
    }

    /**
     * Has Many Audible Settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function audibles()
    {
        return $this->hasMany(UserAudible::class);
    }

    /**
     * Has Many Thanks Given.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function thanksGiven()
    {
        return $this->hasMany(Thank::class, 'user_id', 'id');
    }

    /**
     * Has Many Wish's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishes()
    {
        return $this->hasMany(Wish::class);
    }

    /**
     * Has Many Thanks Received.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function thanksReceived()
    {
        return $this->hasManyThrough(Thank::class, Torrent::class);
    }

    /**
     * Has Many Polls.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * Has Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function torrents()
    {
        return $this->hasMany(Torrent::class);
    }

    /**
     * Has Many Playlist.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Has Many Sent PM's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pm_sender()
    {
        return $this->hasMany(PrivateMessage::class, 'sender_id');
    }

    /**
     * Has Many Received PM's.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pm_receiver()
    {
        return $this->hasMany(PrivateMessage::class, 'receiver_id');
    }

    /**
     * Has Many Peers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function peers()
    {
        return $this->hasMany(Peer::class);
    }

    /**
     * Has Many Followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    /**
     * Has Many Articles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    /**
     * Has Many Topics.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function topics()
    {
        return $this->hasMany(Topic::class, 'first_post_user_id', 'id');
    }

    /**
     * Has Many Posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Has Many Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Has Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requests()
    {
        return $this->hasMany(TorrentRequest::class);
    }

    /**
     * Has Approved Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ApprovedRequests()
    {
        return $this->hasMany(TorrentRequest::class, 'approved_by');
    }

    /**
     * Has Filled Many Torrent Requests.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function FilledRequests()
    {
        return $this->hasMany(TorrentRequest::class, 'filled_by');
    }

    /**
     * Has Many Torrent Request BON Bounties.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestBounty()
    {
        return $this->hasMany(TorrentRequestBounty::class);
    }

    /**
     * Has Moderated Many Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function moderated()
    {
        return $this->hasMany(Torrent::class, 'moderated_by');
    }

    /**
     * Has Many Notes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'user_id');
    }

    /**
     * Has Many Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    /**
     * Has Solved Many Reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solvedReports()
    {
        return $this->hasMany(Report::class, 'staff_id');
    }

    /**
     * Has Many Torrent History.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(History::class, 'user_id');
    }

    /**
     * Has Many Bans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userban()
    {
        return $this->hasMany(Ban::class, 'owned_by');
    }

    /**
     * Has Given Many Bans.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffban()
    {
        return $this->hasMany(Ban::class, 'created_by');
    }

    /**
     * Has Given Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffwarning()
    {
        return $this->hasMany(Warning::class, 'warned_by');
    }

    /**
     * Has Deleted Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffdeletedwarning()
    {
        return $this->hasMany(Warning::class, 'deleted_by');
    }

    /**
     * Has Many Warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userwarning()
    {
        return $this->hasMany(Warning::class, 'user_id');
    }

    /**
     * Has Given Many Invites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentInvite()
    {
        return $this->hasMany(Invite::class, 'user_id');
    }

    /**
     * Has Received Many Invites.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedInvite()
    {
        return $this->hasMany(Invite::class, 'accepted_by');
    }

    /**
     * Has Many Featured Torrents.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function featuredTorrent()
    {
        return $this->hasMany(FeaturedTorrent::class);
    }

    /**
     * Has Many Post Likes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Has Given Many BON Tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bonGiven()
    {
        return $this->hasMany(BonTransactions::class, 'sender');
    }

    /**
     * Has Received Many BON Tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bonReceived()
    {
        return $this->hasMany(BonTransactions::class, 'receiver');
    }

    /**
     * Has Many Subscriptions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Has many free leech tokens.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function freeleechTokens()
    {
        return $this->hasMany(FreeleechToken::class);
    }

    /**
     * Has many warnings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }

    /**
     * Get the Users username as slug.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return Str::slug($this->username);
    }

    /**
     * Get the Users accepts notification as bool.
     *
     * @param self   $sender
     * @param self   $target
     * @param string $group
     * @param bool   $type
     *
     * @return int
     */
    public function acceptsNotification(self $sender, self $target, $group = 'follower', $type = false)
    {
        $target_group = 'json_'.$group.'_groups';
        if ($sender->id === $target->id) {
            return false;
        }
        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }
        if ($target->block_notifications && $target->block_notifications == 1) {
            return false;
        }
        if ($target->notification && $type && (! $target->notification->$type)) {
            return false;
        }
        if ($target->notification && $target->notification->$target_group && is_array($target->notification->$target_group['default_groups'])) {
            if (array_key_exists($sender->group->id, $target->notification->$target_group['default_groups'])) {
                return $target->notification->$target_group['default_groups'][$sender->group->id] == 1;
            }

            return true;
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     *
     * @param self   $target
     * @param string $group
     * @param bool   $type
     *
     * @return int
     */
    public function isVisible(self $target, $group = 'profile', $type = false)
    {
        $target_group = 'json_'.$group.'_groups';
        $sender = auth()->user();
        if ($sender->id == $target->id) {
            return true;
        }
        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }
        if ($target->hidden && $target->hidden == 1) {
            return false;
        }
        if ($target->privacy && $type && (! $target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }
        if ($target->privacy && $target->privacy->$target_group && is_array($target->privacy->$target_group['default_groups'])) {
            if (array_key_exists($sender->group->id, $target->privacy->$target_group['default_groups'])) {
                return $target->privacy->$target_group['default_groups'][$sender->group->id] == 1;
            }

            return true;
        }

        return true;
    }

    /**
     * Get the Users allowed answer as bool.
     *
     * @param self   $target
     * @param string $group
     * @param bool   $type
     *
     * @return int
     */
    public function isAllowed(self $target, $group = 'profile', $type = false)
    {
        $target_group = 'json_'.$group.'_groups';
        $sender = auth()->user();
        if ($sender->id == $target->id) {
            return true;
        }
        if ($sender->group->is_modo || $sender->group->is_admin) {
            return true;
        }
        if ($target->private_profile && $target->private_profile == 1) {
            return false;
        }
        if ($target->privacy && $type && (! $target->privacy->$type || $target->privacy->$type == 0)) {
            return false;
        }
        if ($target->privacy && $target->privacy->$target_group && is_array($target->privacy->$target_group['default_groups'])) {
            if (array_key_exists($sender->group->id, $target->privacy->$target_group['default_groups'])) {
                return $target->privacy->$target_group['default_groups'][$sender->group->id] == 1;
            }

            return true;
        }

        return true;
    }

    /**
     * Does Subscription Exist.
     *
     * @param $type
     * @param $topic_id
     *
     * @return string
     */
    public function isSubscribed(string $type, $topic_id)
    {
        if ($type === 'topic') {
            return (bool) $this->subscriptions()->where('topic_id', '=', $topic_id)->first(['id']);
        }

        return (bool) $this->subscriptions()->where('forum_id', '=', $topic_id)->first(['id']);
    }

    /**
     * Get All Followers Of A User.
     *
     * @param $target_id
     *
     * @return string
     */
    public function isFollowing($target_id)
    {
        return (bool) $this->follows()->where('target_id', '=', $target_id)->first(['id']);
    }

    /**
     * Return Upload In Human Format.
     *
     * @param null $bytes
     * @param int  $precision
     *
     * @return string
     */
    public function getUploaded($bytes = null, $precision = 2)
    {
        $bytes = $this->uploaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return Download In Human Format.
     *
     * @param null $bytes
     * @param int  $precision
     *
     * @return string
     */
    public function getDownloaded($bytes = null, $precision = 2)
    {
        $bytes = $this->downloaded;

        if ($bytes > 0) {
            return StringHelper::formatBytes((float) $bytes, 2);
        }

        return StringHelper::formatBytes(0, 2);
    }

    /**
     * Return The Ratio.
     */
    public function getRatio()
    {
        if ($this->downloaded === 0) {
            return INF;
        }

        return (float) round($this->uploaded / $this->downloaded, 2);
    }

    // Return the ratio pretty formated as a string.
    public function getRatioString()
    {
        $ratio = $this->getRatio();
        if (is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    // Return the ratio after $size bytes would be downloaded.
    public function ratioAfterSize($size)
    {
        if ($this->downloaded + $size == 0) {
            return INF;
        }

        return (float) round($this->uploaded / ($this->downloaded + $size), 2);
    }

    // Return the ratio after $size bytes would be downloaded, pretty formatted
    // as a string.
    public function ratioAfterSizeString($size, $freeleech = false)
    {
        if ($freeleech) {
            return $this->getRatioString().' ('.trans('torrent.freeleech').')';
        }

        $ratio = $this->ratioAfterSize($size);
        if (is_infinite($ratio)) {
            return '∞';
        }

        return (string) $ratio;
    }

    // Return the size (pretty formated) which can be safely downloaded
    // without falling under the minimum ratio.
    public function untilRatio($ratio)
    {
        if ($ratio == 0.0) {
            return '∞';
        }

        $bytes = round(($this->uploaded / $ratio) - $this->downloaded);

        return StringHelper::formatBytes($bytes);
    }

    /**
     * Set The Users Signature After Its Been Purified.
     *
     * @param string $value
     *
     * @return void
     */
    public function setSignatureAttribute($value)
    {
        $antiXss = new AntiXSS();

        $this->attributes['signature'] = $antiXss->xss_clean($value);
    }

    /**
     * Returns the HTML of the user's signature.
     *
     * @return string html
     */
    public function getSignature()
    {
        $bbcode = new Bbcode();
        $linkify = new Linkify();

        return $bbcode->parse($linkify->linky($this->signature), true);
    }

    /**
     * Set The Users About Me After Its Been Purified.
     *
     * @param string $value
     *
     * @return void
     */
    public function setAboutAttribute($value)
    {
        $antiXss = new AntiXSS();

        $this->attributes['about'] = $antiXss->xss_clean($value);
    }

    /**
     * Parse About Me And Return Valid HTML.
     *
     * @return string Parsed BBCODE To HTML
     */
    public function getAboutHtml()
    {
        if (empty($this->about)) {
            return 'N/A';
        }
        $bbcode = new Bbcode();
        $linkify = new Linkify();

        return $bbcode->parse($linkify->linky($this->about), true);
    }

    /**
     * @method getSeedbonus
     *
     * Formats the seebonus of the User
     *
     * @return decimal
     */
    public function getSeedbonus()
    {
        return number_format($this->seedbonus, 2, '.', ' ');
    }

    /**
     * @method getSeeding
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getSeeding()
    {
        return Peer::where('user_id', '=', $this->id)
            ->where('seeder', '=', '1')
            ->distinct('info_hash')
            ->count();
    }

    /**
     * @method getLast30Uploads
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getLast30Uploads()
    {
        $current = Carbon::now();

        return Torrent::withAnyStatus()
            ->where('user_id', '=', $this->id)
            ->where('created_at', '>', $current->copy()->subDays(30)->toDateTimeString())
            ->count();
    }

    /**
     * @method getUploads
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getUploads()
    {
        return Torrent::withAnyStatus()
            ->where('user_id', '=', $this->id)
            ->count();
    }

    /**
     * @method getLeeching
     *
     * Gets the amount of torrents a user seeds
     *
     * @return int
     */
    public function getLeeching()
    {
        return Peer::where('user_id', '=', $this->id)
            ->where('left', '>', '0')
            ->distinct('info_hash')
            ->count();
    }

    /**
     * @method getWarning
     *
     * Gets count on users active warnings
     *
     * @return int
     */
    public function getWarning()
    {
        return Warning::where('user_id', '=', $this->id)
            ->whereNotNull('torrent')
            ->where('active', '=', '1')
            ->count();
    }

    /**
     * @method getTotalSeedTime
     *
     * Gets the users total seedtime
     *
     * @return int
     */
    public function getTotalSeedTime()
    {
        return History::where('user_id', '=', $this->id)
            ->sum('seedtime');
    }

    /**
     * @method getTotalSeedSize
     *
     * Gets the users total seedsoze
     *
     * @return int
     */
    public function getTotalSeedSize()
    {
        $peers = Peer::where('user_id', '=', $this->id)->where('seeder', '=', 1)->pluck('torrent_id');

        return Torrent::whereIn('id', $peers)->sum('size');
    }
}

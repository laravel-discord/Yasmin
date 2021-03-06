<?php
/**
 * Yasmin
 * Copyright 2017-2019 Charlotte Dunois, All Rights Reserved.
 *
 * Website: https://charuru.moe
 * License: https://github.com/CharlotteDunois/Yasmin/blob/master/LICENSE
 */

namespace CharlotteDunois\Yasmin\Models;

use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Interfaces\GuildMemberStorageInterface;
use InvalidArgumentException;

/**
 * Guild Member Storage to store guild members, utilizes Collection.
 */
class GuildMemberStorage extends Storage implements GuildMemberStorageInterface
{
    /**
     * The guild this storage belongs to.
     *
     * @var Guild
     */
    protected $guild;

    /**
     * @param  Client  $client
     * @param  Guild  $guild
     * @param  array|null  $data
     *
     * @internal
     */
    public function __construct(
        Client $client,
        Guild $guild,
        ?array $data = null
    ) {
        parent::__construct($client, $data);
        $this->guild = $guild;

        $this->baseStorageArgs[] = $this->guild;
    }

    /**
     * Resolves given data to a guildmember.
     *
     * @param  GuildMember|User|string|int  $guildmember  string/int = user ID
     *
     * @return GuildMember
     * @throws InvalidArgumentException
     */
    public function resolve($guildmember)
    {
        if ($guildmember instanceof GuildMember) {
            return $guildmember;
        }

        if ($guildmember instanceof User) {
            $guildmember = $guildmember->id;
        }

        if (is_int($guildmember)) {
            $guildmember = (string) $guildmember;
        }

        if (is_string($guildmember) && parent::has($guildmember)) {
            return parent::get($guildmember);
        }

        throw new InvalidArgumentException('Unable to resolve unknown guild member');
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        return parent::has($key);
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     *
     * @return GuildMember|null
     */
    public function get($key)
    {
        return parent::get($key);
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     * @param  GuildMember  $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        parent::set($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     * @param  string  $key
     *
     * @return $this
     */
    public function delete($key)
    {
        parent::delete($key);

        return $this;
    }

    /**
     * Factory to create (or retrieve existing) guild members.
     *
     * @param  array  $data
     *
     * @return GuildMember
     * @internal
     */
    public function factory(array $data)
    {
        if (parent::has($data['user']['id'])) {
            $member = parent::get($data['user']['id']);
            $member->_patch($data);

            return $member;
        }

        $member = new GuildMember($this->client, $this->guild, $data);
        $this->set($member->id, $member);

        return $member;
    }
}

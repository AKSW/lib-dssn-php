<?php
/**
 * Factory for pings
 *
 * @author  {@link http://sebastian.tramp.name Sebastian Tramp}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */
class DSSN_Ping_Factory
{
    /**
     * Create new ping with source, target and comment
     */
    public function withSTC ($sourceIri, $targetIri, $commentLiteral)
    {
        $ping = new DSSN_Ping();
        $ping-setSourceIri($sourceIri);
        $ping-setTargetIri($targetIri);
        $ping-setcommentLiteral($commentLiteral);

        return $ping;
    }
}

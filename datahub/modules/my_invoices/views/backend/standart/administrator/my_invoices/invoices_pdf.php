<page backtop="10mm" backbottom="15mm" backleft="15mm" backright="15mm">
    <page_footer>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; border-top: 1px solid #E2E8F0;">
            <tr>
                <td style="width: 50%; text-align: left; font-size: 7.5pt; color: #94A3B8; padding-top: 8px;">
                    Generated on <?= date('d M Y, H:i'); ?>
                </td>
                <td style="width: 50%; text-align: right; font-size: 7.5pt; color: #94A3B8; padding-top: 8px;">
                    KOL System Finance Billing Hub &bull; Confidential
                </td>
            </tr>
        </table>
    </page_footer>

    <style type="text/css">
        * { font-family: helvetica, Arial, sans-serif; line-height: 1.4; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        table { border-collapse: collapse; }
    </style>

    <!-- 1. Header Area -->
    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: bottom;">
                <div style="font-size: 24pt; font-weight: bold; color: #0F172A; letter-spacing: -0.5px;">INVOICE</div>
                <div style="font-size: 10pt; color: #64748B; margin-top: 4px; font-weight: bold;">#<?= htmlspecialchars($invoice->invoice_no ?? ''); ?></div>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: bottom;">
                <div style="font-size: 16pt; font-weight: bold; color: #1E293B; letter-spacing: -0.5px;">KOL SYSTEM</div>
                <div style="font-size: 9pt; color: #64748B; margin-top: 4px;">Influencer Marketing &amp; Management</div>
            </td>
        </tr>
    </table>
    
    <div style="border-bottom: 2px solid #E2E8F0; margin-top: 15px; margin-bottom: 25px;"></div>

    <!-- 2. Invoice & Campaign Details -->
    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
        <tr>
            <!-- Billed Details -->
            <td style="width: 48%; vertical-align: top;">
                <div style="font-size: 8pt; color: #94A3B8; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">KOL / Creator:</div>
                <div style="font-size: 12pt; font-weight: bold; color: #0F172A; margin-bottom: 4px;"><?= htmlspecialchars($invoice->kol_name ?? 'N/A'); ?></div>
                <div style="font-size: 9.5pt; color: #475569; margin-bottom: 3px;">Campaign: <span style="font-weight: bold; color: #1E293B;"><?= htmlspecialchars($invoice->campaign_name ?? 'N/A'); ?></span></div>
                <?php if (!empty($invoice->npwp_number)): ?>
                    <div style="font-size: 9.5pt; color: #475569;">NPWP: <?= htmlspecialchars($invoice->npwp_number); ?></div>
                <?php endif; ?>
            </td>
            
            <td style="width: 4%;"></td> <!-- Spacer -->

            <!-- Invoice Details -->
            <td style="width: 48%; vertical-align: top;">
                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                    <tr>
                        <td style="width: 45%; font-size: 9pt; color: #64748B; padding: 4px 0;">Date of Issue</td>
                        <td style="width: 55%; font-size: 9.5pt; font-weight: bold; text-align: right; color: #0F172A; padding: 4px 0;">
                            <?= isset($invoice->created_at) ? date('d M Y', strtotime($invoice->created_at)) : '-'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 9pt; color: #64748B; border-top: 1px solid #F1F5F9; padding: 4px 0;">Due Date</td>
                        <td style="font-size: 9.5pt; font-weight: bold; text-align: right; color: #0F172A; border-top: 1px solid #F1F5F9; padding: 4px 0;">
                            <?= !empty($invoice->due_date) ? date('d M Y', strtotime($invoice->due_date)) : '-'; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 9pt; color: #64748B; border-top: 1px solid #F1F5F9; padding: 4px 0;">Payment Status</td>
                        <td style="text-align: right; border-top: 1px solid #F1F5F9; padding: 4px 0;">
                            <?php 
                                $status = $invoice->status ?? 'SUBMITTED';
                                $text_color = '#0284C7';
                                if ($status == 'PAID') { $text_color = '#16A34A'; }
                                elseif ($status == 'APPROVED') { $text_color = '#0284C7'; }
                                elseif ($status == 'SUBMITTED') { $text_color = '#0284C7'; }
                                elseif ($status == 'PARTIAL') { $text_color = '#D97706'; }
                                elseif ($status == 'REJECTED' || $status == 'UNPAID') { $text_color = '#DC2626'; }
                                elseif ($status == 'CANCELLED') { $text_color = '#475569'; }
                            ?>
                            <span style="font-size: 9.5pt; font-weight: bold; color: <?= $text_color; ?>;"><?= $status; ?></span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <br><br>

    <!-- 3. Deliverables / Services -->
    <div style="font-size: 10pt; font-weight: bold; color: #0F172A; margin-bottom: 12px;">Deliverable Performance &amp; Services</div>
    
    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
        <thead>
            <tr>
                <th style="width: 5%; padding: 8px 4px; border-bottom: 2px solid #CBD5E1; color: #475569; font-size: 8pt; font-weight: bold; text-align: center; text-transform: uppercase;">No</th>
                <th style="width: 45%; padding: 8px 4px; border-bottom: 2px solid #CBD5E1; color: #475569; font-size: 8pt; font-weight: bold; text-align: left; text-transform: uppercase;">Description</th>
                <th style="width: 10%; padding: 8px 4px; border-bottom: 2px solid #CBD5E1; color: #475569; font-size: 8pt; font-weight: bold; text-align: center; text-transform: uppercase;">Qty</th>
                <th style="width: 20%; padding: 8px 4px; border-bottom: 2px solid #CBD5E1; color: #475569; font-size: 8pt; font-weight: bold; text-align: right; text-transform: uppercase;">Unit Price</th>
                <th style="width: 20%; padding: 8px 4px; border-bottom: 2px solid #CBD5E1; color: #475569; font-size: 8pt; font-weight: bold; text-align: right; text-transform: uppercase;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $subtotal = $invoice->subtotal ?? 0; ?>
            <tr>
                <td style="width: 5%; border-bottom: 1px solid #E2E8F0; padding: 12px 4px; text-align: center; font-size: 9.5pt; color: #1E293B; vertical-align: top;">1</td>
                <td style="width: 45%; border-bottom: 1px solid #E2E8F0; padding: 12px 4px; font-size: 9.5pt; vertical-align: top;">
                    <div style="font-weight: bold; color: #0F172A; margin-bottom: 4px;">Jasa Influencer / Deliverable KOL</div>
                    <div style="font-size: 8.5pt; color: #64748B;">Campaign: <?= htmlspecialchars($invoice->campaign_name ?? 'N/A'); ?></div>
                </td>
                <td style="width: 10%; border-bottom: 1px solid #E2E8F0; padding: 12px 4px; text-align: center; font-size: 9.5pt; color: #1E293B; vertical-align: top;">1</td>
                <td style="width: 20%; border-bottom: 1px solid #E2E8F0; padding: 12px 4px; text-align: right; font-size: 9.5pt; color: #475569; vertical-align: top;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                <td style="width: 20%; border-bottom: 1px solid #E2E8F0; padding: 12px 4px; text-align: right; font-size: 9.5pt; font-weight: bold; color: #0F172A; vertical-align: top;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <br><br>

    <!-- 4. Payment & Summary -->
    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; margin-top: 10px;">
        <tr>
            <!-- Bank Instruction -->
            <td style="width: 48%; vertical-align: top;">
                <div style="font-size: 10pt; font-weight: bold; color: #0F172A; margin-bottom: 12px;">Payment Instructions</div>
                <table border="0" cellspacing="0" cellpadding="12" style="width: 100%; background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                    <tr>
                        <td>
                            <?php if (!empty($invoice->bank_name)): ?>
                                <div style="font-size: 8pt; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Bank Name</div>
                                <div style="font-size: 10.5pt; font-weight: bold; color: #0F172A; margin-bottom: 12px;"><?= htmlspecialchars($invoice->bank_name); ?></div>
                                
                                <div style="font-size: 8pt; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Account Number</div>
                                <div style="font-size: 11pt; font-weight: bold; color: #0284C7; margin-bottom: 12px;"><?= htmlspecialchars($invoice->bank_account_number); ?></div>
                                
                                <div style="font-size: 8pt; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Account Name</div>
                                <div style="font-size: 10.5pt; font-weight: bold; color: #0F172A;"><?= htmlspecialchars($invoice->bank_account_name); ?></div>
                            <?php else: ?>
                                <div style="font-size: 9.5pt; color: #475569; font-style: italic;">
                                    Please transfer to the official company bank account.
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </td>
            
            <td style="width: 4%;"></td> <!-- Spacer -->
            
            <!-- Payment Summary -->
            <td style="width: 48%; vertical-align: top;">
                <div style="font-size: 10pt; font-weight: bold; color: #0F172A; margin-bottom: 12px;">Payment Summary</div>
                <?php 
                    $tax_amount = $invoice->tax_amount ?? 0;
                    $transfer_total = $subtotal - $tax_amount;
                    $paid_amount = $invoice->amount_paid ?? 0;
                    $balance_due = $transfer_total - $paid_amount;
                ?>
                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                    <tr>
                        <td style="width: 50%; font-size: 9.5pt; color: #475569; padding: 4px 0;">Subtotal</td>
                        <td style="width: 50%; font-size: 9.5pt; text-align: right; font-weight: bold; color: #0F172A; padding: 4px 0;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                    </tr>
                    <?php if ($tax_amount > 0): ?>
                    <tr>
                        <td style="font-size: 9.5pt; color: #475569; padding: 4px 0;">
                            Tax (<?= htmlspecialchars($invoice->tax_type ?? 'PPh'); ?><?= !empty($invoice->tax_rate_percent) && (float)$invoice->tax_rate_percent > 0 ? ' ' . (float)$invoice->tax_rate_percent . '%' : ''; ?>)
                        </td>
                        <td style="font-size: 9.5pt; text-align: right; font-weight: bold; color: #DC2626; padding: 4px 0;">- Rp <?= number_format($tax_amount, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr>
                        <td colspan="2" style="padding: 0;">
                            <div style="border-top: 1px solid #CBD5E1; margin: 8px 0;"></div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="font-size: 11pt; font-weight: bold; color: #0F172A; padding: 6px 0;">Total Transfer</td>
                        <td style="font-size: 12pt; text-align: right; font-weight: bold; color: #16A34A; padding: 6px 0;">Rp <?= number_format($transfer_total, 0, ',', '.'); ?></td>
                    </tr>

                    <?php if ($status == 'PARTIAL' || $paid_amount > 0): ?>
                    <tr>
                        <td colspan="2" style="padding: 0;">
                            <div style="border-top: 1px dashed #CBD5E1; margin: 8px 0;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 9.5pt; color: #475569; padding: 4px 0;">Amount Paid</td>
                        <td style="font-size: 9.5pt; text-align: right; font-weight: bold; color: #0F172A; padding: 4px 0;">Rp <?= number_format($paid_amount, 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size: 10pt; font-weight: bold; color: #DC2626; padding: 4px 0;">Balance Due</td>
                        <td style="font-size: 10.5pt; text-align: right; font-weight: bold; color: #DC2626; padding: 4px 0;">Rp <?= number_format($balance_due, 0, ',', '.'); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </td>
        </tr>
    </table>

    <!-- 5. Signature -->
    <!-- TABEL WRAPPER TANDA TANGAN -->
    <table style="width: 100%; margin-top: 70px;" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <!-- Kolom Kiri Kosong (Spacer) -->
        <td style="width: 68%;"></td>

        <!-- Kolom Kanan (Blok TTD Utuh) -->
        <td style="width: 32%; vertical-align: top;">
          
          <!-- Inner Table agar semua elemen TTD terkunci sejajar di tengah -->
          <table style="width: 180px; margin-left: auto; margin-right: 15px;" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td style="text-align: center; font-size: 10px; color: #777; padding-bottom: 3px;">
                Authorized by:
              </td>
            </tr>
            <tr>
              <td style="text-align: center; font-size: 11px; font-weight: bold; color: #111; padding-bottom: 50px;">
                <?= htmlspecialchars($invoice->kol_name ?? 'Finance & Accounting Dept'); ?>
              </td>
            </tr>
            <tr>
              <!-- Garis Tanda Tangan -->
              <td style="border-bottom: 1px solid #222; height: 1px; font-size: 1px; line-height: 1px;">
                &nbsp;
              </td>
            </tr>
            <tr>
              <td style="text-align: center; font-size: 11px; font-weight: bold; color: #111; padding-top: 6px;">
                <?= htmlspecialchars($invoice->kol_name ?? ($invoice->aauth_users_full_name ?? 'Authorized Signatory')); ?>
              </td>
            </tr>
          </table>

        </td>
      </tr>
    </table>
</page>